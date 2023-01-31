<?php

namespace Evolution\DataModel\Action;

use Evolution\Common\Action\ActionType;
use Evolution\Common\Action\BaseAction;
use Evolution\DataModel\City\CityBuilding;

class BuildAction extends BaseAction
{

    public function __construct(protected CityBuilding $building, protected float $buildEfficiency = 1.0)
    {
        parent::__construct(ActionType::BUILD, "Build {$building->building->name}", $building->building->buildTime);
    }

    public function tour(): float
    {
        return intval($this->building->currentWorkers * $this->buildEfficiency);
    }

    public function afterTour(): void
    {
        $this->building->built = $this->getCompletion();
    }

    public function onComplete(): void
    {
        $this->building->built = 1.0;
    }

}
