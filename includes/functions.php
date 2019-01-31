<?php
function random($length = 10){
    //don't include + or & or # or % because these characters mean something in get or post requests.
    return substr(str_shuffle(str_repeat(";!@$^*()-=_[]{}|,./>?`~:0123456789abcdefghijklmnopqrstuvwxyz", $length)), 0, $length);
    //return md5(uniqid());
    //try this and see how random it is.
    //return base64_encode(openssl_random_pseudo_bytes($length));
    //
    //return mcrypt_create_iv($length);
    //only good in linux
    // if (@is_readable('/dev/urandom')) { 
    //     $f=fopen('/dev/urandom', 'r'); 
    //     $urandom=fread($f, $len); 
    //     fclose($f); 
    // } 

    // $return=''; 
    // for ($i=0;$i<$len;++$i) { 
    //     if (!isset($urandom)) { 
    //         if ($i%2==0) mt_srand(time()%2147 * 1000000 + (double)microtime() * 1000000); 
    //         $rand=48+mt_rand()%64; 
    //     } else $rand=48+ord($urandom[$i])%64; 

    //     if ($rand>57) 
    //         $rand+=7; 
    //     if ($rand>90) 
    //         $rand+=6; 

    //     if ($rand==123) $rand=45; 
    //     if ($rand==124) $rand=46; 
    //     $return.=chr($rand); 
    // }
    // return $return; 
}

function setToken(){
    //http://stackoverflow.com/questions/8350126/csrf-hash-token-behaviour-when-website-is-open-in-multiple-tabs Use an array of token and timestamp and 
    //delete the tokens older than 30 minutes. Then if your token matches any of the tokens from last 30 minutes let it through. That way tabs work.
    //change token to be a unique 13 character string and save it to the session to check when submitting that it is the same. Once a token is used delete it from the session.
    //If the form didn't complete successfully then keep using the same token. If the form completed then delete the token.
    if(!isset($_SESSION['csrf_session_token']) || count($_SESSION['csrf_session_token']) == 0){
       $_SESSION['csrf_session_token'][] = createCSRFToken();
    }
    if(isset($_POST['csrf_token'])){
        //use the last token if we are loading a page from post.
        $csrf_token = end($_SESSION['csrf_session_token'])[0];
    } else {
        $csrf_token = end($_SESSION['csrf_session_token'])[0];
       //print_r($_SESSION); //exit();
    }
    return $csrf_token;
}

function createCSRFToken(){
    $csrf_token = random();
    return array($csrf_token,time());
}

function isTokenValid($token){
    //checks if token is valid
    foreach($_SESSION['csrf_session_token'] as $each){
        if($each[0] == $token){
            return true;
        }
    }
    return false;
}

function deleteToken($token){
    //delete the token with specified string. 
    //Do this after successful form. reuse token after error in form.
    foreach($_SESSION['csrf_session_token'] as $key=>$each){
        if($each[0] == $token){
            //delete token
            unset($_SESSION['csrf_session_token'][$key]);
        }
    }
}

function deleteExpiredTokens(){
    //number of minutes each token lasts
    global $token_life_minutes;
    if(isset($_SESSION['csrf_session_token'])){
        if(is_array($_SESSION['csrf_session_token']) == false) unset($_SESSION['csrf_session_token']);
        foreach($_SESSION['csrf_session_token'] as $key=>$each){
            // echo "key: $key token:{$each[0]} time:{$each[1]}\ntime() - $time." seconds ago\n";
            $time = $each[1];
            if((time() - $time) > 60*$token_life_minutes){
                //delete token
                unset($_SESSION['csrf_session_token'][$key]);
            }
        }
    }
}

function checkCSRFToken($csrf_token, $error){
    global $token_life_minutes;
    if(!isTokenValid($csrf_token)){
        //echo "Your token is invalid!<br />session_token: ".print_r($_SESSION['csrf_session_token'],1)." Form token: {$_POST['csrf_token']}<br /><br />";
        $error[] = "Your form session expired.<br />Forms become stale if left alone for $token_life_minutes minutes for your security.<br /><a href=''>Refresh the page.</a><br />session_token: ".print_r($_SESSION['csrf_session_token'],1)." Form token: {$_POST['csrf_token']}<br /><br />";
    }
    return $error;
}

function hashPassword($password, $newAccount = false){
    //hash 1000 times
    for($x = 0; $x < 1000; $x++){
        $password = sha1($password);
    }
    //check if we are registering a new account. If not then we do the sha1 below in the database when logging in using a select statement to save reading the salt.
    if($newAccount) {
        $salt = random(64);
        $password = sha1('RandomCharactersBeforePassword'.$password.$salt.'AfterSaltRandomCharacters');
        return array($password, $salt);
    }
    return $password;
}

function logout(){
    global $root;
    if(!isset($_SESSION)) session_start();
    session_unset();
    $_SESSION['message'][] = 'You have been logged out';
    header("Location: {$root}");
    exit();
}

function mapTree($dataset) {
    $tree = array();
    foreach ($dataset as $id=>&$node) {
        if(isset($node['parentId']) == false) $node['parentId'] = null;
        if ($node['parentId'] === null) {
            $tree[$id] = &$node;
        } else {
            if(!isset($dataset[$node['parentId']])) $dataset[$node['parentId']] = array();
            if(!isset($dataset[$node['parentId']]['children'])) $dataset[$node['parentId']]['children'] = array();
            $dataset[$node['parentId']]['children'][$id] = &$node;
        }
    }
    
    return $tree;
}

function display_ingredient_tree($nodes, $indent = 0){
    $begin = $indent == 0;
    if(!isset($used_foodGroup)) $used_foodGroup = array();
    if ($indent >= 20) return;  // Stop at 20 sub levels
    foreach ($nodes as $node) {
        if(isset($node['id']) && !in_array($node['id'], $used_foodGroup) && $node['type'] == 'food_group'){
            if($begin)
                echo str_repeat(' ',($indent+3)*4);
            else
                echo str_repeat(' ',($indent)*4);
            $hiddenClass = '';
            $used_foodGroup[$node['id']] = array();
            $used_foodGroup[$node['id']]['begin'] = $node['id'];
            $used_foodGroup[$node['id']]['end'] = false;
            if (!isset($node['children'])){
                //if there is no children then hide
                $hiddenClass = ' noChildren';
            }
            echo "<div>
            ";
            if($begin)
                echo str_repeat(' ',($indent+1)*4);
            else
                echo str_repeat(' ',($indent+2)*4);
            echo "<div class='foodGroup$hiddenClass'><span class='caret-right' onclick='collapseFoodGroup(this, {$node['orig_id']})'></span><li id='foodGroupItem{$node['orig_id']}' onclick='collapseFoodGroup(this, {$node['orig_id']},\"sibling\")'>{$node['orig_id']}:".htmlspecialchars($node['name'])."<span class='btn btn-info' onclick='clickFoodGroup(event,this)'>view/edit</span></li></div>
                ";
            if($begin)
                echo str_repeat(' ',($indent)*4);
            else
                echo str_repeat(' ',($indent+1)*4);
            echo "<ul id='foodGroup{$node['orig_id']}'>
";
        }
        if(isset($node['type']) && $node['type'] == 'ingredient'){

            if($begin)
                echo str_repeat(' ',($indent)*4);
            else
                echo str_repeat(' ',($indent+4)*4);
        }

        //<ul id='foodGroup$foodGroup'>\n
        //<li onclick='clickFoodGroup(this)'>{$node['name']}</li></div>\n";
        //echo "type:".$node['type'];
        if(isset($node['type']) && $node['type'] == 'ingredient'){
            echo "<div class='ingredients' id='ingredient{$node['orig_id']}'><li onclick='pickIngredient(this)'><span class='ingredientName'>".htmlspecialchars($node['name'])."</span><span class='btn btn-info' onclick='clickIngredient(event,this)'>view/edit</span></li></div>\n";
        }
        if (isset($node['children'])){
            if(isset($node['type']) && $node['type'] == 'food_group'){
                if($begin)
                    echo str_repeat(' ',($indent)*4);
                else
                    echo str_repeat(' ',($indent)*4);
            }
            // echo $indent."b<ul>\n";
            display_ingredient_tree($node['children'],$indent+1);
            if($begin)
             echo str_repeat(' ',($indent+4)*4);
            else
             echo str_repeat(' ',($indent+3)*4);
            // echo $indent."e</ul>\n";
        }
        if(isset($node['type']) && $node['type'] == 'food_group' && $used_foodGroup[$node['id']]['end'] == false){
            $used_foodGroup[$node['id']]['end'] = true;
            if($begin)
                echo str_repeat(' ',($indent)*4);
            else
                echo str_repeat(' ',($indent+5)*4);
            echo "</ul> 
";
            if($begin)
                echo str_repeat(' ',($indent+3)*4);
            else
                echo str_repeat(' ',($indent+4)*4);
            echo "</div>\n";
        }
    }
    // if($begin)
    //     echo str_repeat(' ',($indent)*4);
    // else
    //     echo str_repeat(' ',($indent+1)*4);
}

//type can be ajax or normal
function checkLoggedIn($type = 'ajax'){
    if(isset($_SESSION['user_id'])){
        return true;
    } else {
        $message = 'You are not logged on anymore. Login please';
        if($type == 'ajax') 
            echo '{"success":false,"message":"'.$message.'"}';
        else
            echo '<h3>You need to be logged in to see this page. Please Login</h3>';
        return false;
    }
}

function updateMulipleOptionList($recipe_id, $selected_options, $selection_name){
    global $db, $prepend_table;
    //the first integer in the type list will represent the recipe_id
    $type_list = 'i';
    $value_list = array($recipe_id);
    if(count($selected_options) > 0){
    //this will make ?,?, if there are three selected options. The third question mark is already in the ExecuteSQL function below
        $question_mark_list = str_repeat('?,', count($selected_options) - 1);
        //This type list will represent iii if there are three selected options
        $type_list .= str_repeat('i', count($selected_options));
        $value_list = array_merge($value_list, $selected_options);
    }
    //delete the options in the database that are not the selected options
    $sql = "DELETE rt FROM {$prepend_table}recipe_to_{$selection_name} rt WHERE recipe_id = ?";
    //if there are no options selected delete them all
    if(count($selected_options) > 0)
        $sql .= " AND {$selection_name}_id NOT IN({$question_mark_list}?)";
    if($selection_name == 'user_group'){
        //only delete the user_groups that the user can see and isn't selected by the user 
        $sql .= " AND {$selection_name}_id IN (
            SELECT {$selection_name}_id FROM {$prepend_table}user_to_{$selection_name} 
            WHERE user_id = ?
        )";
        $type_list .= 'i';
        $value_list = array_merge($value_list, array($_SESSION['user_id']));
    }
    // echo $sql;
    // echo $type_list;
    // print_r($value_list);

    $delete_result = $db->ExecuteSQL($sql
        , $type_list
        , $value_list);

    //insert the selected options
    //if there are no options we won't insert anything
    if(count($selected_options) == 0) 
        //To "return" means to terminate the function here.
        return;
    
    $question_mark_list = str_repeat('(?,?),', count($selected_options) - 1);
    $type_list = str_repeat('i', count($selected_options) * 2);
    $value_list = [];
    foreach ($selected_options as $each_option) {
        $value_list[] = $recipe_id;
        $value_list[] = $each_option;
    }

    //IGNORE keyword stops errors from happening, when a row in the database already exists.
    $insert_result = $db->ExecuteSQL("INSERT IGNORE INTO {$prepend_table}recipe_to_{$selection_name}
            (recipe_id, {$selection_name}_id) VALUES
            $question_mark_list (?,?)"
        , $type_list
        , $value_list);
}

function canDeleteFoodGroup($id){
    global $db;
    $sql = "SELECT count(*) as count FROM ingredient_map im WHERE food_group_id = ?";
    $data = $db->FetchArray($sql, 'i', $id);
    //stop the function here since we found ingredients
    if($data['count'] != 0){
        return $data['count'];
    }
    $sql = "SELECT food_group_id FROM food_group WHERE parent_food_group_id = ?";
    $food_group_data = $db->FetchAll($sql, 'i', $id);
    foreach($food_group_data as $eachData){
        // echo 'inner food group id:'.$id."\n";
        $count = canDeleteFoodGroup($eachData['food_group_id']);
        if($count != 0) return $count;
    }
}

function deleteFoodGroup($id){
    global $db;
    $sql = "SELECT food_group_id FROM food_group WHERE parent_food_group_id = ?";
    $food_group_data = $db->FetchAll($sql, 'i', $id);
    foreach($food_group_data as $eachData){
        // echo 'inner food group id:'.$id."\n";
        deleteFoodGroup($eachData['food_group_id']);
        if($count != 0) return $count;
    }
    $sql = "DELETE FROM food_group WHERE food_group_id = ?";
    //echo $sql.' '.$id."\n";
    $db->ExecuteSQL($sql, 'i', $id);
}

/***
 * description: Used when editing a food group. We want to know if a food group has a parent equal to the food group we are editing. 
 * returns boolean
*/
function isFoodGroupAncestorOfFoodGroup($food_group_id, $ancestor_food_group_id){
    global $db;
    $sql = "SELECT parent_food_group_id FROM food_group WHERE food_group_id = ?";
    $food_group_data = $db->FetchAll($sql, 'i', $food_group_id);
    $parent_food_group_id = $food_group_data[0]['parent_food_group_id'];
    if($food_group_id == $ancestor_food_group_id || $parent_food_group_id == null)
        return false;
    else if($parent_food_group_id == $ancestor_food_group_id)
        return true;
    else
        return isFoodGroupAncestorOfFoodGroup($parent_food_group_id, $ancestor_food_group_id);
}

































