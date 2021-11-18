<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Description of Adapter
 *
 * @author Alexandru
 */
interface File_Adapter
{
    public function save_original($file);
    public function process_upload($file);
}
