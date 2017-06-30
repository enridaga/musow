<?php
require_once 'arc2-2.2.4/ARC2.php';
$source = "https://docs.google.com/spreadsheets/d/1DNIRiQTcs8ZJ2ELm6vBMgTyHyhPO1Kh4mCEWRKr3vlM/pub?output=csv";

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
		
		// "Project",
		triple ( $ID, $ns . 'ontology/project', v("Project",$line));
		
		// "Search Criterion",
		triple ( $ID, $ns . 'ontology/searchCriterion', v("Search Criterion",$line ));
		// "Research Questions",
		triple ( $ID, $ns . 'ontology/researchQuestions', v("Research Questions",$line));
		// "Item:Resource example",
		triple ( $ID, $ns . 'ontology/item/resourceExample', v("Item:Resource example",$line));
		// "Reused resource",
		triple ( $ID, $ns . 'ontology/item/reusedResource', v("Reused resource",$line));
		// "Resource type",
		triple ( $ID, $ns . 'ontology/item/resourceType', v("Resource type",$line ));
		// "Category",
		triple ( $ID, $ns . 'ontology/category', $ns . 'category/' . v("Category",$line ) , 'uri');
		
		// "Type: Collection",
		literal ( $ID, $ns . 'ontology/type/collection', (v("Type: Collection",$line) == 'T' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );
		// "Type: Specification",
		literal ( $ID, $ns . 'ontology/type/specification', (v("Type: Specification",$line) == 'T' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );
		// "Affordance: Is playable?",
		literal ( $ID, $ns . 'ontology/affordance/playable', (v("Affordance: Is playable?",$line) == 'T' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );
		
		// "Purpose: Learning",
		literal ( $ID, $ns . 'ontology/purpose/learning', (v("Purpose: Learning",$line ) == 'T' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );
		
		// "Purpose: Research",
		literal ( $ID, $ns . 'ontology/purpose/research', (v("Purpose: Research",$line)== 'T' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );
		
		// "Scope: Temporal",
		literal ( $ID, $ns . 'ontology/scope/temporal', (v("Scope: Temporal",$line) == 'T' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );
		// "Scope: Geographical",
		literal ( $ID, $ns . 'ontology/scope/geographical', (v("Scope: Geographical",$line) == 'T' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );
		// "Scope: Genre",
		literal ( $ID, $ns . 'ontology/scope/genre', (v("Scope: Genre",$line) == 'T' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );
		// "Scope: Artist",
		literal ( $ID, $ns . 'ontology/scope/artist', (v("Scope: Artist",$line) == 'T' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );
		// "Scope: Formats",
		triple ( $ID, $ns . 'ontology/scope/formats', v("Scope: Formats",$line ));
		// "Scope: MO type",
		triple ( $ID, $ns . 'ontology/scope/musicOntologyType', v("Scope: MO type",$line) ); // Use music ontology URI
		// "Scope: Object type",
		triple ( $ID, $ns . 'ontology/scope/objectType', v("Scope: Object type",$line) );
		
		// "Access: Public",
		literal ( $ID, $ns . 'ontology/access/public', (v("Access: Public",$line)=='T' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );
		// "Access: license",
		triple ( $ID, $ns . 'ontology/access/license', v("Access: license",$line  ) );
		// "Access: Free/Charged",
		triple ( $ID, $ns . 'ontology/access/type', v("Access: Free/Charged", $line ) );
		// "Format: Interoperable?",
		literal( $ID, $ns . 'ontology/format/interperable', (v("Format: Interoperable?",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		// "Interface: Human consumption?",
		literal( $ID, $ns . 'ontology/interface/human', (v("Interface: Human consumption?",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		// "Interface: API?",
		literal( $ID, $ns . 'ontology/interface/api', (v("Interface: API?",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		// "Interface: SPARQL endpoint?",
		literal( $ID, $ns . 'ontology/interface/sparql', (v("Interface: SPARQL endpoint?",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		// "SPARQL endpoint URI",
		literal( $ID, $ns . 'ontology/sparqlEndpoint', (v("SPARQL endpoint URI",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		// "Interface: Data Dump?",
		literal( $ID, $ns . 'ontology/interface/dump', (v("Interface: Data Dump?",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		// "Interface: Is it queryable?",
		literal( $ID, $ns . 'ontology/interface/queryable', (v("Interface: Is it queryable?",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		// "Interface: Browsable?",
		literal( $ID, $ns . 'ontology/interface/browsable', (v("Interface: Browsable?",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		// "Collection: Size",
		triple( $ID, $ns . 'ontology/collection/size', (v("Collection: Size",$line)));
		// "Data size",
		triple( $ID, $ns . 'ontology/data/size', (v("Data size",$line)));
		// "Symbolic: Machine readable?",
		literal( $ID, $ns . 'ontology/symbolic/machineReadable', (v("Symbolic: Machine readable?",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		// "Feature: Melody",
		literal( $ID, $ns . 'ontology/feature/melody', (v("Feature: Melody",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		
		// "Feature: Harmony",
		literal( $ID, $ns . 'ontology/feature/harmony', (v("Feature: Harmony",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		// "Feature: Lyrics",
		literal( $ID, $ns . 'ontology/feature/lyrics', (v("Feature: Lyrics",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		// "Feature: Rhythm",
		literal( $ID, $ns . 'ontology/feature/rhythm', (v("Feature: Rhythm",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		// "Feature: Timbre",
		literal( $ID, $ns . 'ontology/feature/timbre', (v("Feature: Timbre",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		// "Feature: Contour/Shape",
		literal( $ID, $ns . 'ontology/feature/shape', (v("Feature: Contour/Shape",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		// "Feature: Structure",
		literal( $ID, $ns . 'ontology/feature/structure', (v("Feature: Structure",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		// "Feature: Descriptive Metadata",
		literal( $ID, $ns . 'ontology/feature/metadata', (v("Feature: Descriptive Metadata",$line)=='T'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		// "Situation/Task",
		triple( $ID, $ns . 'ontology/situation/task', v("Situation/Task", $line));
		// "Target audience");
		triple( $ID, $ns . 'ontology/situation/target', v("Target audience", $line));
	}
	
	fclose ( $handle );
} else {
	// error opening the file.
}

$ser = ARC2::getNtriplesSerializer ();
header ( 'Content-Type:', 'text/plain:charset=utf-8', 200 );
print $ser->getSerializedTriples ( $triples );
