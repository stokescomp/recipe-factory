<style>
ul{
	margin:0;
	padding:0 0 0 20px;
}
li{
	padding:0;
	margin:0;
}
</style>
<?php
	//.02 seconds for 1000 nodes. 110 times faster
	//.03 seconds for 10000 nodes. 2066 faster
	//.8 seconds for 100,000 nodes
	require 'tree_fast.php';
	//.2 seconds for 1000 nodes
	//62 seconds for 10000 nodes
	// require 'tree_slow.php';
// Build a random dataset
$maxNodes = 10;
$dataset = array();
for ($i=0;$i<$maxNodes;$i++) {
	$parent = rand(-1,$i-1);	// We use $i rather than maxNodes here to avoid infinite loops
	if ($parent == -1) $parent = null;
	$dataset[$i] = array(
		'id'=>$i,
		'name'=>'Node '.$i,
		'parentId'=>$parent
	);
}

// Initialize variables
$startTime = 0;
$endTime = 0;
$tree = array();

echo "<pre>";
print_r($dataset);

// Build tree
$startTime = microtime(true);
$tree = mapTree($dataset);
$endTime = microtime(true);

// Print benchmark
print '<h2>Benchmark</h2>';
print ($endTime-$startTime).' seconds<br>';

print_r($tree);
//exit;
// Print tree
print "<h2>Tree</h2>\n";
display_tree_pretty($tree);

// Print dataset
// print '<h2>Dataset '.(count($dataset>100)?'(partial)':'').'</h2>';
// print '<pre>';
// if (count($dataset>100)) {
// 	print_r(array_slice($dataset,0,100));
// } else {
// 	print_r($dataset);
// }



// Support Function

function display_tree($nodes, $indent=0) {
	if ($indent >= 20) return;	// Stop at 20 sub levels
	
	foreach ($nodes as $node) {
		print str_repeat('&nbsp;',$indent*4);
		print $node['name'];
		print '<br/>';
		if (isset($node['children']))
			display_tree($node['children'],$indent+1);
	}
}

function display_tree_pretty($nodes, $indent=0) {
	$begin = $indent == 0;
	if ($indent >= 20) return;	// Stop at 20 sub levels
	if($begin)
		echo str_repeat(' ',$indent*4);
	else
		echo str_repeat(' ',($indent+1)*4);
	echo "<ul>\n";
	foreach ($nodes as $node) {
		if($begin)
			echo str_repeat(' ',($indent+1)*4);
		else
			echo str_repeat(' ',($indent+2)*4);

		echo "<li>indent:".$indent.' '.$node['name']."</li>\n";
		if (isset($node['children'])){
			// if($begin)
			// 	echo str_repeat(' ',($indent+1)*4);
			// else
			// 	echo str_repeat(' ',($indent+3)*4);
			// echo $indent."b<ul>\n";
			display_tree_pretty($node['children'],$indent+1);
			// if($begin)
			// 	echo str_repeat(' ',($indent+1)*4);
			// else
			// 	echo str_repeat(' ',($indent+3)*4);
			// echo $indent."e</ul>\n";
		}
	}
	if($begin)
		echo str_repeat(' ',($indent)*4);
	else
		echo str_repeat(' ',($indent+1)*4);
	echo "</ul>\n";
}
            
// 0q<ul>
//         <li>Node 0</li>
//     0b<ul>
//         1q<ul>
//             <li>Node 1</li>
//             <li>Node 2</li>
//     1z</ul>0e</ul>
// 0z</ul>