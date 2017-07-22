<?php

$out = trim(shell_exec("nm -DC libminecraftpe.so | grep '.*::.*'")); // do not change
$out = explode("\n", $out);
$out = array_filter($out, "trim");

$outPath = __DIR__ . "/stuff";
$title = "";

echo "> mcpe-extractor" . PHP_EOL . PHP_EOL;

// check if libminecraftpe.so exists
if(!is_file(__DIR__ . "/libminecraftpe.so")) {
	die("Cannot find libminecraftpe.so" . PHP_EOL);
}

// create folders
echo "Creating folders..." . PHP_EOL;
makeFolders();

echo "Extracting and writing to files...";
foreach($out as $line) {
	$line = explode(' ', $line, 2);
	$class = substr($line[1], 0, strpos($line[1], "::"));
	
	// get the class name
	if(strpos($class, "std") === false and strpos($class, "void") === false
		and strpos($class, "vtable") === false
		and strpos($class, "_") === false
		and strpos($class, "(") === false
		and strpos($class, "virtual") === false
		and strpos($class, "float") === false
		and strpos($class, "typeinfo") === false
		and strpos($class, "void );") === false
		and strpos($class, "bool") === false
		and strpos($class, "int") === false
		and strpos($class, "&") === false
		and strpos($class, "<") === false) {
		
		$title = substr($class, 2);
	}
	
	// get the function name
	$function = substr($line[1], strpos($line[1], "::"));
	$function = substr($function, 0, strpos($function, ")"));
	if(strpos($function, "_") === false and strpos($function, "<") === false 
		and strpos($function, "+") === false
		and strpos($function, "vtable") === false
		and strpos($function, "void") === false) {
		
		$method = str_replace("::", "", "void $function);");
		
		if(strpos($method, "get") !== false) {
			$method = $method . " const;";
		}
	}
	$path = "$outPath";
	
	// sort files into directories
	if(isClass("Screen")) $path = "$outPath/client/screen";
	if(isClass("Gui")) $path = "$outPath/client/gui";
	if(isClass("ImageButton") or isClass("Tab") or isClass("Label")) $path = "$outPath/client/gui/elements";
	if(isClass("App")) $path = "$outPath/client";
	if(isClass("Packet")) $path = "$outPath/packet";
	if(isClass("Player") and !isClass("Packet")) $path = "$outPath/player";
	if(isClass("Minecraft")) $path = "$outPath/client";
	if(isClass("Entity")) $path = "$outPath/entity";
	if(isClass("Block")) $path = "$outPath/block";
	if(isClass("Item")) $path = "$outPath/item";
	if(isClass("Rak")) $path = "$outPath/raknet";
	if(isClass("Tag")) $path = "$outPath/nbt";
	if(isClass("World")) $path = "$outPath/world";
	if(isClass("Biome")) $path = "$outPath/world/biome";
	if(isClass("Command")) $path = "$outPath/command";
	if(isClass("HTTP") or isClass("Request")) $path = "$outPath/http";
	if(isClass("Store")) $path = "$outPath/store";
	if(isClass("Render")) $path = "$outPath/render";
	if(isClass("Goal")) $path = "$outPath/goal";
	if(isClass("Particle")) $path = "$outPath/entity/particle";
	if(isClass("Enchant")) $path = "$outPath/item/enchantment";
	if(isClass("Dimension")) $path = "$outPath/world/dimension";
	if(isClass("Mob") and !isClass("Particle") and !isClass("Spawner")) $path = "$outPath/entity";
	if(isClass("Realms")) $path = "$outPath/realms";
	if(isClass("ResourcePack")) $path = "$outPath/resourcepack";

	file_put_contents($path . "/$title.txt", $method . PHP_EOL, FILE_APPEND);
}

echo "Done!";

function makeFolders() {
	global $outPath;
	$paths = [
		"/client",
		"/client/screen",
		"/client/gui",
		"/client/gui/elements",
		"/packet",
		"/player",
		"/raknet",
		"/nbt",
		"/item",
		"/item/enchantment",
		"/block",
		"/entity",
		"/entity/particle",
		"/command",
		"/http",
		"/store",
		"/render",
		"/goal",
		"/realms",
		"/resourcepack",
		"/world",
		"/world/biome",
		"/world/dimension"
	];
	if(!is_dir($outPath)) {
		mkdir($outPath);
	}
	foreach($paths as $path) {
		if(!is_dir($outPath . $path)) {
			mkdir($outPath . $path);
		}
	}
}

function isClass($text) {
	global $title;
	return strpos($title, $text) !== false;
}
