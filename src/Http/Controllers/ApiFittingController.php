<?php

namespace CryptaTech\Seat\Fitting\Http\Controllers;

use Seat\Api\Http\Controllers\Api\v2\ApiController;

/**
 * Class ApiFittingController.
 *
 * @package CryptaTech\Seat\Fitting\Http\Controllers
 */
class ApiFittingController extends ApiController
{
  public function getFittingList()
    {
    return FittingController::getFittingList();
  }

  public function getFittingById($id)
    {
    return FittingController::getFittingById($id);
  }

  public function getDoctrineList()
    {
    return FittingController::getDoctrineList();
  }

  public function getDoctrineList($id)
    {
    return FittingController::getDoctrineById($id);
  }
}
