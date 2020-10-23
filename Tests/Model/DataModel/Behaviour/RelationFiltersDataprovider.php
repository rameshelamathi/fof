<?php
/**
 * @package   FOF
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 2, or later
 */

class RelationFiltersDataprovider
{
    public static function getTestOnAfterBuildQuery()
    {
        $data[] = array(
            array(
                'operator' => '>=',
                'value'    => 1
            ),
            array(
                'case'  => 'Parent with 1 or more children',
                'query' => "SELECT *
FROM test
WHERE ((
SELECT COUNT(*)
FROM `#__fakeapp_children` AS `reltbl`
WHERE `reltbl`.`fakeapp_parent_id` = `#__fakeapp_parents`.`fakeapp_parent_id`) >= '1')"
            )
        );

        $data[] = array(
            array(
                'operator' => '==',
                'value'    => 1
            ),
            array(
                'case'  => 'Parent with 1 child',
                'query' => "SELECT *
FROM test
WHERE ((
SELECT COUNT(*)
FROM `#__fakeapp_children` AS `reltbl`
WHERE `reltbl`.`fakeapp_parent_id` = `#__fakeapp_parents`.`fakeapp_parent_id`) = '1')"
            )
        );

        $data[] = array(
            array(
                'operator' => '()',
                'value'    => array(1,3)
            ),
            array(
                'case'  => 'Parent with 1-3 child',
                'query' => "SELECT *
FROM test
WHERE (((
SELECT COUNT(*)
FROM `#__fakeapp_children` AS `reltbl`
WHERE `reltbl`.`fakeapp_parent_id` = `#__fakeapp_parents`.`fakeapp_parent_id`) >= 1) AND ((
SELECT COUNT(*)
FROM `#__fakeapp_children` AS `reltbl`
WHERE `reltbl`.`fakeapp_parent_id` = `#__fakeapp_parents`.`fakeapp_parent_id`) <= 3))"
            )
        );

        $data[] = array(
            array(
                'operator' => ')(',
                'value'    => array(1,3)
            ),
            array(
                'case'  => 'Parent with less than 1 OR more than 3 children',
                'query' => "SELECT *
FROM test
WHERE (((
SELECT COUNT(*)
FROM `#__fakeapp_children` AS `reltbl`
WHERE `reltbl`.`fakeapp_parent_id` = `#__fakeapp_parents`.`fakeapp_parent_id`) < 1) OR ((
SELECT COUNT(*)
FROM `#__fakeapp_children` AS `reltbl`
WHERE `reltbl`.`fakeapp_parent_id` = `#__fakeapp_parents`.`fakeapp_parent_id`) > 3))"
            )
        );

        $data[] = array(
            array(
                'operator' => '*=',
                'value'    => array(
                    'value'    => 2,
                    'interval' => 2
                )
            ),
            array(
                'case'  => 'Parent with value 2, interval 2 children',
                'query' => "SELECT *
FROM test
WHERE (((
SELECT COUNT(*)
FROM `#__fakeapp_children` AS `reltbl`
WHERE `reltbl`.`fakeapp_parent_id` = `#__fakeapp_parents`.`fakeapp_parent_id`) >= 0) AND ((
SELECT COUNT(*)
FROM `#__fakeapp_children` AS `reltbl`
WHERE `reltbl`.`fakeapp_parent_id` = `#__fakeapp_parents`.`fakeapp_parent_id`) <= 4))"
            )
        );

        return $data;
    }
}
