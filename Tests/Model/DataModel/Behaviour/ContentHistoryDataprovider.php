<?php
/**
 * @package   FOF
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 2, or later
 */

class ContentHistoryDataprovider
{
    public static function getTestOnAfterSave()
    {
        $data[] = array(
            'input' => array(
                'save_history' => 1
            ),
            'check' => array(
                'case' => 'Component enables history saving',
                'store' => 1
            )
        );

        $data[] = array(
            'input' => array(
                'save_history' => 0
            ),
            'check' => array(
                'case' => 'Component does not enable history saving',
                'store' => 0
            )
        );

        return $data;
    }

    public static function getTestOnBeforeDelete()
    {
        $data[] = array(
            'input' => array(
                'save_history' => 1
            ),
            'check' => array(
                'case' => 'Component enables history saving',
                'delete' => 1
            )
        );

        $data[] = array(
            'input' => array(
                'save_history' => 0
            ),
            'check' => array(
                'case' => 'Component does not enable history saving',
                'delete' => 0
            )
        );

        return $data;
    }
}
