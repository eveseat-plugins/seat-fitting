<?php

namespace CryptaTech\Seat\Fitting\Http\Controllers;

use CryptaTech\Seat\Fitting\Helpers\CalculateConstants;
use CryptaTech\Seat\Fitting\Helpers\CalculateEft;
use CryptaTech\Seat\Fitting\Models\Doctrine;
use CryptaTech\Seat\Fitting\Models\Fitting;
use CryptaTech\Seat\Fitting\Models\FittingItem;
use CryptaTech\Seat\Fitting\Validation\DoctrineValidation;
use CryptaTech\Seat\Fitting\Validation\FittingValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Seat\Eveapi\Models\Alliances\Alliance;
use Seat\Eveapi\Models\Character\CharacterAffiliation;
use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Eveapi\Models\Corporation\CorporationInfo;
use Seat\Eveapi\Models\Sde\DgmTypeAttribute;
use Seat\Eveapi\Models\Sde\InvType;
use Seat\Web\Http\Controllers\Controller;
use RecursiveTree\Seat\PricesCore\Facades\PriceProviderSystem;

class FittingController extends Controller implements CalculateConstants
{
    use CalculateEft;

    private $requiredSkills = [];

    public function getSettings()
    {
        $provider = setting('cryptatech_seat_fitting_price_provider', true);
        return view('fitting::settings', compact(['provider']));
    }

    public function saveSettings(Request $request)
    {

        $request->validate([
            "price_source" => "required|integer"
        ]);

        setting(["cryptatech_seat_fitting_price_provider", $request->price_source], true);

        return redirect()->back()->with('success', 'Updated settings');
    }

    public function getDoctrineEdit($doctrine_id)
    {
        $selected = [];
        $unselected = [];
        $doctrine_fits = [];

        $fittings = Fitting::all();
        $doctrine_fittings = Doctrine::find($doctrine_id)->fittings()->get();

        foreach ($doctrine_fittings as $doctrine_fitting) {
            array_push($doctrine_fits, $doctrine_fitting->fitting_id);
        }

        foreach ($fittings as $fitting) {
            $entry = [
                'id' => $fitting->fitting_id,
                'shiptype' => $fitting->ship->typeName,
                'fitname' => $fitting->name,
                'typeID' => $fitting->ship->typeID,
            ];

            if (array_search($fitting->fitting_id, $doctrine_fits) !== false) {
                array_push($selected, $entry);
            } else {
                array_push($unselected, $entry);
            }
        }

        return [
            $selected,
            $unselected,
            $doctrine_id,
            Doctrine::find($doctrine_id)->name,
        ];
    }

    public function getDoctrineList()
    {
        $doctrine_names = [];

        $doctrines = Doctrine::all();

        if (count($doctrines) > 0) {

            foreach ($doctrines as $doctrine) {
                array_push($doctrine_names, [
                    'id' => $doctrine->id,
                    'name' => $doctrine->name,
                ]);
            }
        }

        return $doctrine_names;
    }

    public function getDoctrineById($id)
    {
        $fitting_list = [];

        $doctrine = Doctrine::find($id);
        $fittings = $doctrine->fittings()->get();

        foreach ($fittings as $fitting) {
            $ship = $fitting->ship;

            array_push($fitting_list, [
                'id' => $fitting->fitting_id,
                'name' => $fitting->name,
                'shipType' => $fitting->ship->typeName,
                'shipImg' => $ship->typeID,
            ]);
        }

        return $fitting_list;
    }

    public function delDoctrineById($id)
    {
        Doctrine::destroy($id);

        return 'Success';
    }

    public function deleteFittingById($id)
    {
        Fitting::destroy($id);

        return 'Success';
    }

    public function getSkillsByFitId($id)
    {
        $characters = [];
        $skillsToons = [];

        $fitting = Fitting::find($id);
        $skillsToons['skills'] = $this->calculate($fitting);
        $skilledCharacters = CharacterInfo::with('skills')->whereIn('character_id', auth()->user()->associatedCharacterIds())->get();

        foreach ($skilledCharacters as $character) {

            $index = $character->character_id;

            $skillsToons['characters'][$index]['id'] = $character->character_id;
            $skillsToons['characters'][$index]['name'] = $character->name;

            foreach ($character->skills as $skill) {

                $rank = DgmTypeAttribute::where('typeID', $skill->skill_id)->where('attributeID', '275')->first();

                $skillsToons['characters'][$index]['skill'][$skill->skill_id]['level'] = $skill->trained_skill_level;
                $skillsToons['characters'][$index]['skill'][$skill->skill_id]['rank'] = $rank->valueFloat;
            }

            // Fill in missing skills so Javascript doesn't barf and you have the correct rank
            foreach ($skillsToons['skills'] as $skill) {

                if (isset($skillsToons['characters'][$index]['skill'][$skill['typeId']])) {
                    continue;
                }

                $rank = DgmTypeAttribute::where('typeID', $skill['typeId'])->where('attributeID', '275')->first();

                $skillsToons['characters'][$index]['skill'][$skill['typeId']]['level'] = 0;
                $skillsToons['characters'][$index]['skill'][$skill['typeId']]['rank'] = $rank->valueFloat;
            }
        }

        return json_encode($skillsToons);
    }

    protected function getFittings()
    {
        return Fitting::all();
    }

    public function getFittingList()
    {
        $fitnames = [];

        $fittings = $this->getFittings();

        if (count($fittings) <= 0)
            return $fitnames;

        foreach ($fittings as $fit) {
            array_push($fitnames, [
                'id' => $fit->fitting_id,
                'shiptype' => $fit->ship->typeName,
                'fitname' => $fit->name,
                'typeID' => $fit->ship_type_id,
            ]);
        }

        return $fitnames;
    }

    public function getEftFittingById($id)
    {
        $fitting = Fitting::find($id);

        return $fitting->toEve();
    }

    public function getFittingCostById($id)
    {
        $fit = Fitting::find($id);
        $provider = setting('cryptatech_seat_fitting_price_provider', true);
        $items = $fit->fitItems;
        $ship = new FittingItem();
        $ship->type_id = $fit->ship_type_id;
        $ship->quantity = 1;
        $items->push($ship);

        // $eft = implode("\n", $fit->eftfitting);
        try {
            PriceProviderSystem::getPrices($provider,$items);
        } catch (PriceProviderException $e) {
            $message = $e->getMessage();
            return redirect()->back()->with("error", "Failed to get prices from price provider: $message");
        }

        $total = $items->sum(function(FittingItem $v){
            return $v->getPrice();
        });


        return response()->json(json_encode(["total" => $total, "ship" => $ship->getPrice()]));
    }

    public function getFittingById($id)
    {
        $fitting = Fitting::find($id);

        $response = $this->fittingParser($fitting);

        $response['exportLinks'] = collect(config('fitting.exportlinks'))->map(function ($link) use ($fitting) {
            return [
                'name' => $link['name'],
                'url' => isset($link['url']) ? $link['url'] . "?id=$fitting->fitting_id" : route($link['route'], ['id' => $fitting->fitting_id]),
            ];
        })->values();

        return response()->json($response);
    }

    public function getFittingView()
    {
        $corps = [];
        $fitlist = $this->getFittingList();

        if (Gate::allows('global.superuser')) {
            $corpnames = CorporationInfo::all();
        } else {
            $corpids = CharacterAffiliation::whereIn('character_id', auth()->user()->associatedCharacterIds())->select('corporation_id')->get()->toArray();
            $corpnames = CorporationInfo::whereIn('corporation_id', $corpids)->get();
        }

        foreach ($corpnames as $corp) {
            $corps[$corp->corporation_id] = $corp->name;
        }

        return view('fitting::fitting', compact('fitlist', 'corps'));
    }

    public function getDoctrineView()
    {
        $doctrine_list = $this->getDoctrineList();

        return view('fitting::doctrine', compact('doctrine_list'));
    }

    public function getAboutView()
    {
        return view('fitting::about');
    }

    public function saveFitting(FittingValidation $request)
    {
        $fitting = new Fitting();

        if ($request->fitSelection > 0) {
            $fit = Fitting::createFromEve($request->eftfitting, $request->fitSelection);
        } else {
            $fit = Fitting::createFromEve($request->eftfitting);
        }        

        $fitlist = $this->getFittingList();

        return view('fitting::fitting', compact('fitlist'));
    }

    public function postFitting(FittingValidation $request)
    {
        $eft = $request->input('eftfitting');

        return response()->json($this->fittingParser($eft));
    }

    private function fittingParser($fit)
    {
        $jsfit = [];

        $jsfit['eft'] = $fit->toEve();
        $jsfit['shipname'] = $fit->ship->typeName;
        $jsfit['fitname'] = $fit->name;
        $jsfit['dronebay'] = []; // Lets load fighters in here too xD
        foreach ($fit->items as $ls) {
            
            switch ($ls->flag){
                case Fitting::BAY_DRONE:
                case Fitting::BAY_FIGHTER:
                    if (isset($jsfit['dronebay'][$ls->type_id])){
                        $jsfit['dronebay'][$ls->type_id]['qty'] += $ls->quantity;
                    } else {
                        $jsfit['dronebay'][$ls->type_id] = ['qty' => $ls->quantity, 'name' => $ls->type->typeName];
                    }
                    break;

                case Fitting::BAY_CARGO: // Not included in the JS response :)
                    break;

                default:
                    $jsfit[$ls->invFlag->flagName] = ['id' => $ls->type_id, 'name' => $ls->type->typeName];
                    break;
            }
            
        }
        return $jsfit;
    }


    public function postSkills(FittingValidation $request)
    {
        $skillsToons = [];
        $fitting = $request->input('eftfitting');
        $skillsToons['skills'] = $this->calculate($fitting);

        $characters = $this->getUserCharacters(auth()->user()->id);

        foreach ($characters as $character) {
            $index = $character->characterID;

            $skillsToons['characters'][$index] = [
                'id' => $character->characterID,
                'name' => $character->characterName,
            ];

            //            $characterSkills = $this->getCharacterSkillsInformation($character->characterID);
            $characterSkills = CharacterInfo::with('skills')->where('character_id', $character->characterID)->get();

            foreach ($characterSkills as $skill) {
                $rank = DgmTypeAttributes::where('typeID', $skill->typeID)->where('attributeID', '275')->first();

                $skillsToons['characters'][$index]['skill'][$skill->typeID] = [
                    'level' => $skill->level,
                    'rank' => $rank->valueFloat,
                ];
            }

            // Fill in missing skills so Javascript doesn't barf and you have the correct rank
            foreach ($skillsToons['skills'] as $skill) {

                if (isset($skillsToons['characters'][$index]['skill'][$skill['typeId']])) {
                    continue;
                }

                $rank = DgmTypeAttributes::where('typeID', $skill['typeId'])->where('attributeID', '275')->first();

                $skillsToons['characters'][$index]['skill'][$skill['typeId']] = [
                    'level' => 0,
                    'rank' => $rank->valueFloat,
                ];
            }
        }

        return response()->json($skillsToons);
    }

    private function getSkillNames($types)
    {
        $skills = [];

        foreach ($types as $skill_id => $level) {
            $res = InvType::where('typeID', $skill_id)->first();

            $skills[] = [
                'typeId' => $skill_id,
                'typeName' => $res->typeName,
                'level' => $level,
            ];
        }

        ksort($skills);

        return $skills;
    }

    public function saveDoctrine(DoctrineValidation $request)
    {
        $doctrine = new Doctrine();

        if ($request->doctrineid > 0) {
            $doctrine = Doctrine::find($request->doctrineid);
        }

        $doctrine->name = $request->doctrinename;
        $doctrine->save();

        foreach ($request->selectedFits as $fitId) {
            $doctrine->fittings()->sync($request->selectedFits);
        }

        return redirect()->route('cryptafitting::doctrineview');
    }

    public function viewDoctrineReport()
    {
        $doctrines = Doctrine::all();
        $corps = CorporationInfo::all();
        $alliances = [];

        $allids = [];

        foreach ($corps as $corp) {
            if (!is_null($corp->alliance_id)) {
                array_push($allids, $corp->alliance_id);
            }
        }

        $alliances = Alliance::whereIn('alliance_id', $allids)->get();

        return view('fitting::doctrinereport', compact('doctrines', 'corps', 'alliances'));
    }

    public function runReport($alliance_id, $corp_id, $doctrine_id)
    {
        $characters = collect();

        if ($alliance_id !== '0') {

            $chars = CharacterInfo::with('skills')->whereHas('affiliation', function ($affiliation) use ($alliance_id) {
                $affiliation->where('alliance_id', $alliance_id);
            })->get();
            $characters = $characters->concat($chars);
        } else {
            $characters = CharacterInfo::with('skills')->whereHas('affiliation', function ($affiliation) use ($corp_id) {
                $affiliation->where('corporation_id', $corp_id);
            })->get();
        }

        $doctrine = Doctrine::where('id', $doctrine_id)->first();
        $fittings = $doctrine->fittings;
        $charData = [];
        $fitData = [];
        $data = [];
        $data['fittings'] = [];
        $data['totals'] = [];
        foreach ($characters as $character) {
            $charData[$character->character_id]['name'] = $character->name;
            $charData[$character->character_id]['skills'] = [];

            foreach ($character->skills as $skill) {
                $charData[$character->character_id]['skills'][$skill->skill_id] = $skill->trained_skill_level;
            }
        }

        foreach ($fittings as $fitting) {
            $fit = Fitting::find($fitting->fitting_id);

            array_push($data['fittings'], $fit->name);

            $this->requiredSkills = [];
            $shipSkills = $this->calculateIndividual($fit->ship_type_id);

            foreach ($shipSkills as $shipSkill) {
                $fitData[$fitting->fitting_id]['shipskills'][$shipSkill['typeId']] = $shipSkill['level'];
            }

            $this->requiredSkills = [];
            $fitSkills = $this->calculate($fit);
            $fitData[$fitting->fitting_id]['name'] = $fit->name;

            foreach ($fitSkills as $fitSkill) {
                $fitData[$fitting->fitting_id]['skills'][$fitSkill['typeId']] = $fitSkill['level'];
            }
        }

        foreach ($charData as $char) {

            foreach ($fitData as $fit) {
                $canflyfit = true;
                $canflyship = true;

                foreach ($fit['skills'] as $skill_id => $level) {
                    if (isset($char['skills'][$skill_id])) {
                        if ($char['skills'][$skill_id] < $level) {
                            $canflyfit = false;
                        }
                    } else {
                        $canflyfit = false;
                    }
                }

                foreach ($fit['shipskills'] as $skill_id => $level) {
                    if (isset($char['skills'][$skill_id])) {
                        if ($char['skills'][$skill_id] < $level) {
                            $canflyship = false;
                        }
                    } else {
                        $canflyship = false;
                    }
                }

                if (!isset($data['totals'][$fit['name']]['ship'])) {
                    $data['totals'][$fit['name']]['ship'] = 0;
                }
                if (!isset($data['totals'][$fit['name']]['fit'])) {
                    $data['totals'][$fit['name']]['fit'] = 0;
                }

                $data['chars'][$char['name']][$fit['name']]['ship'] = false;
                if ($canflyship) {
                    $data['chars'][$char['name']][$fit['name']]['ship'] = true;
                    $data['totals'][$fit['name']]['ship']++;
                }

                $data['chars'][$char['name']][$fit['name']]['fit'] = false;
                if ($canflyfit) {
                    $data['chars'][$char['name']][$fit['name']]['fit'] = true;
                    $data['totals'][$fit['name']]['fit']++;
                }
            }
        }

        $data['totals']['chars'] = count($charData);

        return response()->json($data);
    }
}
