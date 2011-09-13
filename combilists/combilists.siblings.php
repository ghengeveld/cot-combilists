<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.list.tags
[END_COT_EXT]
==================== */

defined('COT_CODE') or die('Wrong URL');

$cl_categories = explode(',', $cfg['plugin']['combilists']['categories']);
$struct = explode('.', $structure['page'][$c]['path']);
$parent = $struct[count($struct)-2];

if (in_array($parent, $cl_categories))
{
	$all_count = 0;
	$siblings = cot_structure_children('page', $parent, false, false);
	foreach($siblings as $sibling)
	{
		$sub_count = null;
		if($cache && $cache->mem)
		{
			if ($cache->mem->exists('subcount_'.$sibling, 'combilists'))
			{
				$sub_count = $cache->mem->get('subcount_'.$sibling, 'combilists');
			}
		}
		if($sub_count === null)
		{
			$sub_count = $db->query("SELECT SUM(structure_count) FROM $db_structure WHERE
				structure_path LIKE '".$db->prep($structure['page'][$sibling]['rpath']).".%' OR
				structure_path = ".$db->quote($structure['page'][$sibling]['rpath']))->fetchColumn();
			$cache && $cache->mem && $cache->mem->store('subcount_'.$sibling, $sub_count, 'combilists');
		}
		$all_count += $sub_count;
		$t->assign(array(
			'LIST_SIBLING_CODE' => $sibling,
			'LIST_SIBLING_TITLE' => $structure['page'][$sibling]['title'],
			'LIST_SIBLING_DESC' => $structure['page'][$sibling]['desc'],
			'LIST_SIBLING_ICON' => $structure['page'][$sibling]['icon'],
			'LIST_SIBLING_URL' => cot_url('page', 'c='.$sibling),
			'LIST_SIBLING_COUNT' => $sub_count
		));
		$t->parse('MAIN.LIST_SIBLINGS');
	}
	$t->assign('LIST_ALL_COUNT', $all_count);
}

?>