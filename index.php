<?php
function process($s){
	$s=trim($s);
	$s=str_replace("\xEF\xBB\xBF", "", $s);     // zero-width space
	$s=str_replace("\xE2\x80\x99", "'", $s);    // right single quote
	$s=str_replace("\xE2\x80\x98", "'", $s);    // left single quote
	$s=str_replace("\xE2\x80\x93", "-", $s);    // en dash to dash
	$s=str_replace("\xE2\x80\xA2", "*", $s);    // bullet to star
	$s=str_replace("\x0B", "\n", $s);   // new line on old macs and ms-dos  
	$magic_quotes_active=get_magic_quotes_gpc();
	$new_enough_php=function_exists("mysql_real_escape_string"); // PHP >= v4.3
	if($new_enough_php){
		if($magic_quotes_active){
			$s=stripslashes($s);
		}
		$s=addslashes($s);
	}else{  // before PHP v4.3.0
		if(!$magic_quotes_active){ 
			// if magic quotes aren't already on this add slashes manually
			$s=addslashes($s);
		}   //if magic quotes are active, then the slashes already exist
	} 
	return($s);
}

if(isset($_POST['submit'])){
	$ombd_id='tt3896198&apikey=c27fbfe3';  //API Key
	$s_query=process($_POST['title']); //search query
	$omdb_api=file_get_contents("http://www.omdbapi.com/?i=$ombd_id&t=$s_query&plot=full"); //API
	$json=json_decode($omdb_api, true);
	$css="#FFF";
	if(strpos(strtolower($s_query), 'red')!==false) $css="#FCC";
	if(strpos(strtolower($s_query), 'green')!==false) $css="#CFC";
	if(strpos(strtolower($s_query), 'blue')!==false) $css="#9CF";
	if(strpos(strtolower($s_query), 'yellow')!==false) $css="#FFC";
	
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
body {
	background-color: #333;
}
.search {
	width: 500px;
	height: 40px;
	margin: 150px auto;
	background: #444;
	background: rgba(0,0,0,.2);
	border-radius: 3px;
	border: 1px solid #fff;
}
.search input {
	width: 370px;
	padding: 10px 5px;
	float: left;
	color: #ccc;
	border: 0;
	background: transparent;
	border-radius: 3px 0 0 3px;
}
.search input:focus {
	outline: 0;
	background: transparent;
}
.search button {
	position: relative;
	float: right;
	border: 0;
	padding: 0;
	cursor: pointer;
	height: 40px;
	width: 120px;
	color: #fff;
	background: transparent;
	border-left: 1px solid #fff;
	border-radius: 0 3px 3px 0;
}
.search button:hover {
	background: #fff;
	color: #444;
}
.search button:active {
	box-shadow: 0px 0px 12px 0px rgba(225, 225, 225, 1);
}
.search button:focus {
	outline: 0;
}
#search_result {
	width: 750px;
	height: auto;
	margin: 150px auto;
	background: <?php echo $css; ?>;
	border-radius: 3px;
	border: 1px solid #fff;
}
.mv_detail {
	margin: 10px;
}
</style>
<title>Movie Search</title>
</head>

<body>
<div id="search_form">
  <form class="search" action="index.php" method="POST" id="movie_search">
    <input type="search" name="title" placeholder="Movie search..." required>
    <button type="submit" name="submit">Search</button>
  </form>
</div>
<?php
	if(isset($_POST['submit'])){
		echo "<div id='search_result'>";
		while(list($key, $val)=each($json)){
			echo "<div class='mv_detail'><strong>$key: </strong>$val</div>";
		}
		echo "</div>";
	}
?>
</body>
</html>