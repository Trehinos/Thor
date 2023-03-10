<?php

namespace June\Process;
enum Priority: int
{

    case MAX = 0xFF;
    case AVG = 0x80;
    case MIN = 0x00;

}
