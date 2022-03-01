<?php

namespace Thor\Web\Assets;

enum AssetType: string
{

    case STYLE = 'css';
    case SCRIPT = 'js';
    case IMG_PNG = 'png';
    case IMG_JPG = 'jpg';

}
