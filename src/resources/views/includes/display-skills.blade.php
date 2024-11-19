<div class="card card-primary card-solid" id="skills-box">
    <div class="card-header form-group"><h3 class="box-title"
                                            id="skill-title">{{trans('fitting::fitting.required_skills_title')}}</h3>
    </div>
    <div class="card-body">
        <div id="skills-window">
            <table class="table table-condensed">
                <tr>
                    <td><span class="fa fa-square "
                              style="color: #5ac597"></span> {{trans('fitting::fitting.skills_required_level_header')}}
                    </td>
                    <td>
                        <span class="fa fa-square text-green"></span> {{trans('fitting::fitting.skills_required_exceeded_header')}}
                    </td>
                    <td>
                        <span class="fa fa-circle text-danger"></span> {{trans('fitting::fitting.skills_required_missing_level_header')}}
                    </td>
                    <td>
                        <span class="fa fa-circle text-green"></span> {{trans('fitting::fitting.skills_required_empty_level_header')}}
                    </td>
                </tr>
            </table>
            <select id="characterSpinner" class="form-control"></select>
            <table style="width: 100%" class="table table-condensed table-striped">
                <thead>
                <tr>
                    <th>{{trans('fitting::fitting.skill_name_header')}}</th>
                    <th style="width: 80px">{{trans('fitting::fitting.skill_level_header')}}</th>
                </tr>
                </thead>
                <tbody id="skillbody">
                <tr>
                    <td colspan="2">{{trans('fitting::fitting.no_character_selected')}}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
