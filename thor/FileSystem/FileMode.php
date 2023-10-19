<?php

namespace Thor\FileSystem;

enum FileMode:string
{

    case READ = 'r';
    case READ_WRITE = 'r+';
    case TRUNCATE_WRITE = 'w';
    case TRUNCATE_READ_WRITE = 'w+';
    case APPEND_WRITE = 'a';
    case APPEND_READ_WRITE = 'a+';
    case EXCLUSIVE_WRITE = 'x';
    case EXCLUSIVE_READ_WRITE = 'x+';
    case LOCK_WRITE = 'c';
    case LOCK_READ_WRITE = 'c+';
    case CLOSE_ON_EXEC = 'e';

}
