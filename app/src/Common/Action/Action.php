<?php

namespace Evolution\Common\Action;

interface Action
{

    public function getName(): string;

    public function getType(): ActionType;

    public function getDuration(): float;

    public function advance(): void;

    public function getCompletion(): float;

    public function onComplete(): void;

    public function isComplete(): bool;


}
