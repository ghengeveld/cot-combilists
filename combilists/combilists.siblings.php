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
		$cachename = 'subcount_'.$sibling;
		if ($sqlfilters && $filterway)
		{
			$cachename .= '_' . md5(implode($filterway,$sqlfilters));
		}
		elseif ($o && $p)
		{
			if (!is_array($o)) $o = array($o);
			if (!is_array($p)) $p = array($p);
			$filters = array_combine($o, $p);
			ksort($filters);
			foreach ($filters as $key => $val)
			{
				$cachename .= "_$key=$val";
			}
		}
		if($cache && $cache->mem)
		{
			if ($cache->mem->exists($cachename, 'combilists'))
			{
				$sub_count = (int)$cache->mem->get($cachename, 'combilists');
			}
		}
		if($sub_count === null)
		{
			$where = array();
			$params = array();
			$categories = implode("','", cot_structure_children('page', $sibling));
			if ($categories)
			{
				$where['cat'] = "page_cat IN ('$categories')";
			}
			$where = ($where) ? 'WHERE ' . implode(' AND ', $where) : '';
			if ($where && $sqlfilters && $filterway) $where .= ' AND (' . implode(" $filterway ", $sqlfilters) . ')';
			if (is_array($sqlparams))
			{
				$params = array_merge($params, $sqlparams);
			}
			$sub_count = (int)$db->query("SELECT COUNT(*) FROM $db_pages $where", $params)->fetchColumn();
			$cache && $cache->mem && $cache->mem->store($cachename, $sub_count, 'combilists');
		}
		$all_count += $sub_count;
		$sibling_url_path = $list_url_path;
		$sibling_url_path['c'] = $sibling;
		$t->assign(array(
			'LIST_SIBLING_CODE' => $sibling,
			'LIST_SIBLING_TITLE' => $structure['page'][$sibling]['title'],
			'LIST_SIBLING_DESC' => $structure['page'][$sibling]['desc'],
			'LIST_SIBLING_ICON' => $structure['page'][$sibling]['icon'],
			'LIST_SIBLING_URL' => cot_url('page', $sibling_url_path),
			'LIST_SIBLING_COUNT' => $sub_count
		));
		$t->parse('MAIN.LIST_SIBLINGS');
	}
	$t->assign('LIST_ALL_COUNT', $all_count);
}

?>