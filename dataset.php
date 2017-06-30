<?php
require_once 'arc2-2.2.4/ARC2.php';
$source = "https://docs.google.com/spreadsheets/d/1DNIRiQTcs8ZJ2ELm6vBMgTyHyhPO1Kh4mCEWRKr3vlM/pub?output=csv";
$dimensionsFile = "https://docs.google.com/spreadsheets/d/1DNIRiQTcs8ZJ2ELm6vBMgTyHyhPO1Kh4mCEWRKr3vlM/pub?gid=275215384&single=true&output=csv";

// Prepare dimensions
$handle = fopen ( $dimensionsFile, 'r' );
if ($handle) {
	$first = TRUE;
	global $types, $dimensions;
	$types = FALSE;
	while ( ($line = fgetcsv ( $handle )) !== false ) {
		if ($first) {
			$first = FALSE;
			$types = $line;
			continue;
		}
		$dimensions[$line[0]] = $line;
	}	
	fclose ( $handle );
} else {
	// error opening the file.
}


$handle = fopen ( $source, 'r' );

global $triples;
$triples = array ();

// s the subject value (a URI, Bnode ID, or Variable)
// p the property URI (or a Variable)
// o the object value (a URI, Bnode ID, Literal, or Variable)
// s_type "uri", "bnode", or "var"
// o_type "uri", "bnode", "literal", or "var"
// o_datatype a datatype URI
// o_lang a language identifier, e.g. ("en-us")
function triple($s, $p, $o, $s_type = "uri", $o_type = "literal", $o_lang = NULL, $o_datatype = NULL) {
	
	if(!$o) return;
	
	global $triples;
	$t = array (
			's' => $s,
			'p' => $p,
			'o' => $o,
			's_type' => $s_type,
			'o_type' => $o_type 
	);
	if ($o_lang) {
		$t ['o_lang'] = $o_lang;
	} elseif ($o_datatype) {
		$t ['o_datatype'] = $o_datatype;
	}
	
	array_push ( $triples, $t );
}
function literal($s, $p, $o, $o_datatype = NULL, $o_lang = NULL) {
	triple($s, $p, $o,  $s_type = "uri", $o_type = "literal", $o_lang, $o_datatype);
}
function v($column, $line){
	global $index;
	return $line[array_search($column, $index)];
}
function d($dimension, $type){
	global $types,$dimensions;
	return ($dimensions[$dimension][array_search($type, $types)] == 'T');
}
$ns = "http://data.open.ac.uk/mudow/";
if ($handle) {
	$first = TRUE;
	global $index;
	$index = FALSE;
	while ( ($line = fgetcsv ( $handle )) !== false ) {
		if ($first) {
			$first = FALSE;
			$index = $line;
			continue;
		}
		$md5 = md5($line[1]);
		$ID = $ns . 'item/' . $md5; // Use Resource Name
		                            // print_r($line);
		                            // "Person",
		triple ( $ID, 'http://www.w3.org/2004/02/skos/core#note', 'Inserted by ' . $line [0] );
		triple ( $ID, 'http://www.w3.org/2004/02/skos/core#note', 'Inserted by ' . $line [0] );

		// "ID: Resource name",
		triple ( $ID, 'http://www.w3.org/2000/01/rdf-schema#label', v("ID: Resource name", $line));

		// "Description",
		triple ( $ID, 'http://purl.org/dc/terms/description', v("Description", $line ) );
		triple ( $ID, 'http://www.w3.org/2000/01/rdf-schema#comment', v("Description", $line ) );
		
		// "ID: URL",
		triple ( $ID, 'http://www.w3.org/ns/dcat#landingPage', v("ID: URL", $line) );
		triple( $ID, 'http://purl.org/dc/terms/identifier', $md5);
		$t = v("Resource type",$line );
		
		// "Project",
		triple ( $ID, $ns . 'ontology/project', v("Project",$line));
		
		// "Search Criterion",
		triple ( $ID, $ns . 'ontology/searchCriterion', v("Search Criterion",$line ));
		// "Research Questions",
		triple ( $ID, $ns . 'ontology/researchQuestions', v("Research Questions",$line));
		if(d("Item:Resource example",$t))
		triple ( $ID, $ns . 'ontology/item/resourceExample', v("Item:Resource example",$line));
		// "Reused resource",
		triple ( $ID, $ns . 'ontology/item/reusedResource', v("Reused resource",$line));
		// "Resource type",
		triple ( $ID, $ns . 'ontology/item/resourceType', v("Resource type",$line ));
		triple ( $ID, $ns . 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', $ns . 'type/' . v("Resource type",$line ), 'uri');
		
		// "Category",
		triple ( $ID, $ns . 'ontology/category', $ns . 'category/' . v("Category",$line ) , 'uri');
		
		// "Type: Collection",
		if(d("Type: Collection",$t)) literal ( $ID, $ns . 'ontology/type/collection', (v("Type: Collection",$line) == 'T' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );

		if(d("Type: Specification",$t)) literal ( $ID, $ns . 'ontology/type/specification', (v("Type: Specification",$line) == 'T' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );
		if(d("Affordance: Is playable?",$t))
		literal ( $ID, $ns . 'ontology/affordance/playable', (v("Affordance: Is playable?",$line) == 'T' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );
		
		if(d("Purpose: Learning",$t))
		literal ( $ID, $ns . 'ontology/purpose/learning', (v("Purpose: Learning",$line ) == 'T' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );
		
		if(d("Purpose: Research",$t))
		literal ( $ID, $ns . 'ontology/purpose/research', (v("Purpose: Research",$line)== 'T' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );
		
		if(d("Scope: Temporal",$t))
		literal ( $ID, $ns . 'ontology/scope/temporal', (v("Scope: Temporal",$line) == 'T' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );
		if(d("Scope: Geographical",$t))
		literal ( $ID, $ns . 'ontology/scope/geographical', (v("Scope: Geographical",$line) == 'T' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );
		if(d("Scope: Genre",$t))
		literal ( $ID, $ns . 'ontology/scope/genre', (v("Scope: Genre",$line) == 'T' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );
		if(d("Scope: Artist",$t))
		literal ( $ID, $ns . 'ontology/scope/artist', (v("Scope: Artist",$line) == 'T' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );
		if(d("Scope: Formats",$t))
		triple ( $ID, $ns . 'ontology/scope/formats', v("Scope: Formats",$line ));
		if(d("Scope: MO type",$t))
		triple ( $ID, $ns . 'ontology/scope/musicOntologyType', v("Scope: MO type",$line) ); // Use music ontology URI
		if(d("Scope: Object type",$t))
		triple ( $ID, $ns . 'ontology/scope/objectType', v("Scope: Object type",$line) );
		
		// "Access: Public",
		literal ( $ID, $ns . 'ontology/access/public', (v("Access: Public",$line)=='T' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );
		// "Access: license",
		triple ( $ID, $ns . 'ontology/access/license', v("Access: license",$line  ) );
		// "Access: Free/Charged",
		triple ( $ID, $ns . 'ontology/access/type', v("Access: Free/Charged", $line ) );
		if(d("Format: Interoperable?",$t))
		literal( $ID, $ns . 'ontology/format/interperable', (v("Format: Interoperable?",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if(d("Interface: Human consumption?",$t))
		literal( $ID, $ns . 'ontology/interface/human', (v("Interface: Human consumption?",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if(d("Interface: API?",$t))
		literal( $ID, $ns . 'ontology/interface/api', (v("Interface: API?",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if(d("Interface: SPARQL endpoint?",$t))
		literal( $ID, $ns . 'ontology/interface/sparql', (v("Interface: SPARQL endpoint?",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if(d("SPARQL endpoint URI",$t))
		literal( $ID, $ns . 'ontology/sparqlEndpoint', (v("SPARQL endpoint URI",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if(d("Interface: Data Dump?",$t))
		literal( $ID, $ns . 'ontology/interface/dump', (v("Interface: Data Dump?",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if(d("Interface: Is it queryable?",$t))
		literal( $ID, $ns . 'ontology/interface/queryable', (v("Interface: Is it queryable?",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if(d("Interface: Browsable?",$t))
		literal( $ID, $ns . 'ontology/interface/browsable', (v("Interface: Browsable?",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if(d("Collection: Size",$t))
		triple( $ID, $ns . 'ontology/collection/size', (v("Collection: Size",$line)));
		if(d("Data size",$t))
		triple( $ID, $ns . 'ontology/data/size', (v("Data size",$line)));
		if(d("Symbolic: Machine readable?",$t))
		literal( $ID, $ns . 'ontology/symbolic/machineReadable', (v("Symbolic: Machine readable?",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if(d("Feature: Melody",$t))
		literal( $ID, $ns . 'ontology/feature/melody', (v("Feature: Melody",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		
		if(d("Feature: Harmony",$t))
		literal( $ID, $ns . 'ontology/feature/harmony', (v("Feature: Harmony",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if(d("Feature: Lyrics",$t))
		literal( $ID, $ns . 'ontology/feature/lyrics', (v("Feature: Lyrics",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if(d("Feature: Rhythm",$t))
		literal( $ID, $ns . 'ontology/feature/rhythm', (v("Feature: Rhythm",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if(d("Feature: Timbre",$t))
		literal( $ID, $ns . 'ontology/feature/timbre', (v("Feature: Timbre",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if(d("Feature: Contour/Shape",$t))
		literal( $ID, $ns . 'ontology/feature/shape', (v("Feature: Contour/Shape",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if(d("Feature: Structure",$t))
		literal( $ID, $ns . 'ontology/feature/structure', (v("Feature: Structure",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if(d("Feature: Descriptive Metadata",$t))
		literal( $ID, $ns . 'ontology/feature/metadata', (v("Feature: Descriptive Metadata",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if(d("Situation/Task",$t))
		triple( $ID, $ns . 'ontology/situation/task', v("Situation/Task", $line));
		if(d("Target audience",$t))
		triple( $ID, $ns . 'ontology/situation/target', v("Target audience", $line));
	}
	
	fclose ( $handle );
} else {
	// error opening the file.
}

$ser = ARC2::getNtriplesSerializer ();
header ( 'Content-Type:', 'text/plain:charset=utf-8', 200 );
print $ser->getSerializedTriples ( $triples );
