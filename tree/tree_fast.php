<?php

function mapTree($dataset) {
	$tree = array();
	foreach ($dataset as $id=>&$node) {
		if ($node['parentId'] === null) {
			$tree[$id] = &$node;
		} else {
			if (!isset($dataset[$node['parentId']]['children'])) $dataset[$node['parentId']]['children'] = array();
			$dataset[$node['parentId']]['children'][$id] = &$node;
		}
	}
	
	return $tree;
}

// function mapTree2(array $dataset) {
//     $tree = array();
 
//     /* Most datasets in the wild are enumerative arrays and we need associative array
//        where the same ID used for addressing parents is used. We make associative
//        array on the fly */
//     $references = array();
//     foreach ($dataset as $id => &$node) {
//         // Add the node to our associative array using it's ID as key
//         $references[$node['id']] = &$node;
 
//         // Add empty placeholder for children
//         $node['children'] = array();
 
//         // It it's a root node, we add it directly to the tree
//         if (is_null($node['parentId'])) {
//             $tree[$node['id']] = &$node;
//         } else {
//             // It was not a root node, add this node as a reference in the parent.
//             $references[$node['parentId']]['children'][$node['id']] = &$node;
//         }
//     }
 
//     return $tree;
// }

// function mapTree3(array $dataset) {
// 	$tree = array();

// 	foreach ($dataset as $node) {
// 		$id = $node['id'];

// 		// add empty array of children
// 		if (!isset($tree[$id]['children'])) {
// 			$tree[$id]['children'] = array();
// 		}

// 		// set created children
// 		$node['children'] = $tree[$id]['children'];

// 		// add node to array
// 		$tree[$id] = $node;

// 		// set parsed node as child of its parent
// 		$tree[$node['parentId']]['children'][$node['id']] = &$tree[$id];
// 	}

// 	return $tree;
// }

// function mapTree($data){
// 	$tree = array();
// 	foreach($data as &$v){
// 		// Get childs
// 		if(isset($tree[$v['id']])) $v['child'] =& $tree[$v['id']];

// 		// push node into parent
// 		$tree[$v['parentId']][$v['id']] =& $v;

// 		// push child into node
// 		$tree[$v['id']]	=& $v['child'];
// 	}

// 	// return Tree
// 	return $tree;
// }

include_once('tree_demo.php');