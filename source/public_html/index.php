<?php
/**
 * This is the root test entry point.
 */
error_reporting(E_ALL ^ E_STRICT);
include ('../lib/Spd.php');

$parser = Spd::getParser(Spd::PARSER_HTML, '../../../Formulator/source/lib');
//dbg($parser);
$parser->parse();
?><head>
<style type="text/css">
body {
	width: 600px;
	margin: 0 auto;
	font-size: 16px;
	font-family: Verdana;
}
h2 {
	font-size: 1.4em;
	font-weight:normal;
	background: #FFF1DE;
	padding: 1em;
	margin:0 -0.7em;
}
h2 code {
	font-weight:bold;
}
h3 {
	font-size: 1em;
}
ul li {
	margin: 0.2em;
}
code.name {
	font-weight:bold;
	color: #a00;
}
.access_modifiers {
}
.class_doc {
	padding:1em;
	padding-top: 0;
	border: 1px solid #aaa;
	margin-bottom: 4em;
}
.class_hierarchy, .elements {
	margin-left: 4em;
}
.elements p {
	font-size: 0.8em;
	color: #333;
}
.inherited {
	font-size: 0.8em;
}
.inherited span {
	font-size: 0.8em;
	font-style:italic;
	display:block;
}
</style>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$(".class_doc").children("h2").each(function(i,e){
		$(e).after('<br />');
		$(e).after('<br />');
		$(e).after('<a href="#class_' + $(e).children("a").attr("id") + '" class="hide_inherited">Hide inherited</a>&nbsp;');
		$(e).after('<a href="#top">Back to top</a>&nbsp;');
	});
	$(".hide_inherited").click(hideInherited);
});

function hideInherited (e) {
	e.preventDefault();
	var pr = $(e.target).attr("href");
	$(pr + " .inherited").hide();
	$(e.target).text("Show inherited");
	$(e.target).click(showInherited);
}
function showInherited (e) {
	e.preventDefault();
	var pr = $(e.target).attr("href");
	$(pr + " .inherited").show();
	$(e.target).text("Hide inherited");
	$(e.target).click(hideInherited);
}
</script>
</head>
<body>
	<a id="top"></a>
	<?php echo $parser->getOutput(); ?>
</body>

<?php
function dbg ($x) {
	echo "<pre>" . var_export($x,1) . "</pre>";
}
?>