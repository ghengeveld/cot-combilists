<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.list.query
[END_COT_EXT]
==================== */

defined('COT_CODE') or die('Wrong URL');

$cl_categories = explode(',', $cfg['plugin']['combilists']['categories']);

if (in_array($c, $cl_categories))
{
	$categories = implode("','", cot_structure_children('page', $c));
	if ($categories)
	{
		$where['cat'] = "page_cat IN ('$categories')";
	}
}

?>