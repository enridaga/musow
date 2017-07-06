<?php
require_once 'arc2-2.2.4/ARC2.php';
$source = "https://docs.google.com/spreadsheets/d/1DNIRiQTcs8ZJ2ELm6vBMgTyHyhPO1Kh4mCEWRKr3vlM/pub?gid=0&single=true&output=csv";
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
// var_dump($types);
// var_dump($dimensions);
// die;
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
	return ($dimensions[$dimension][array_search($type, $types)] == 'Y');
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
		$ID = $ns . $md5; // Use Resource Name
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
		triple ( $ID, 'http://xmlns.com/foaf/0.1/homepage', trim(v("ID: URL", $line)),'uri', 'uri' );
		triple( $ID, 'http://purl.org/dc/terms/identifier', $md5);
		$t = v("Resource type",$line );
		
		// "Project",
		$project = md5(v("Project",$line));
		triple ( $ID, 'http://purl.org/cerif/frapo/isOutputOf', $ns . $project,'uri', 'uri' );
		triple ( $ns . $project, 'http://www.w3.org/2000/01/rdf-schema#label', v("Project",$line));

		// "Search Criterion",
		$subjectTerm = md5(v("Search Criterion",$line ));
		triple ( $ID, 'http://purl.org/spar/fabio/hasSubjectTerm', $ns . $subjectTerm,'uri', 'uri' );
		triple ( $ns . $subjectTerm, 'http://www.w3.org/2000/01/rdf-schema#label', v("Search Criterion",$line ));
		
		// "Research Questions",
		triple ( $ID, 'http://dbpedia.org/ontology/projectObjective', v("Research Questions",$line));
		// Resource example
		if(d("Item:Resource example",$t))
		triple ( $ID, 'http://www.w3.org/2004/02/skos/core#example', v("Item:Resource example",$line),'uri', 'uri' );
		
		// "Reused resource",
		$reusedLine = v("Reused resource",$line);
		$resArray = explode( ';', $reusedLine );
		foreach ($resArray as $value) {
    		triple ( $ID, 'http://www.w3.org/2004/02/skos/core#related', $ns . md5($value),'uri', 'uri' );
    		triple ( $ns . md5($value), 'http://www.w3.org/2000/01/rdf-schema#label', trim($value));
		}
		
		// "Resource type",
		triple ( $ID, 'http://purl.org/spar/datacite/hasGeneralResourceType', $ns . 'type/' . md5(v("Resource type",$line )),'uri', 'uri' );
		triple ( $ns . 'type/' . md5(v("Resource type",$line )), 'http://www.w3.org/2000/01/rdf-schema#label', v("Resource type",$line ));
		
		// "Category",
		triple ( $ID, 'http://dbpedia.org/ontology/category', $ns . 'category/' . v("Category",$line ),'uri', 'uri' );
		triple ($ns . 'category/' . v("Category",$line ), 'http://www.w3.org/2000/01/rdf-schema#label', v("Category",$line ));
		
		// "Type: Collection",
		// if(d("Type: Collection",$t)) literal ( $ID, $ns . 'ontology/type/collection', (v("Type: Collection",$line) == 'Y' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );
		if((v("Type: Collection",$line)) === 'Y') 
			triple ( $ID, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', $ns . 'ontology/type/Collection' ,'uri', 'uri' );

		//if(d("Type: Specification",$t)) literal ( $ID, $ns . 'ontology/type/specification', (v("Type: Specification",$line) == 'Y' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );
		if((v("Type: Specification",$line)) === 'Y') 
			triple ( $ID, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', $ns . 'ontology/type/Specification' ,'uri', 'uri' );
		// "Affordance: Is playable?"
		//if(d("Affordance: Is playable?",$t))
		//literal ( $ID, $ns . 'ontology/affordance/playable', (v("Affordance: Is playable?",$line) == 'Y' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );
		if((v("Affordance: Is playable?",$line)) === 'Y') 
		{	triple ($ID, 'http://schema.org/featureList', $ns . 'ontology/feature/playable' ,'uri', 'uri'  );
			triple ($ns . 'ontology/feature/playable', 'http://www.w3.org/2000/01/rdf-schema#label', 'Playable');
		}
		// "Purpose: Learning",
		//if(d("Purpose: Learning",$t))
		//literal ( $ID, $ns . 'ontology/purpose/learning', (v("Purpose: Learning",$line ) == 'Y' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );
		if((v("Purpose: Learning",$line)) === 'Y') 
		{	triple ($ID, 'http://www.w3.org/ns/oa#hasPurpose', $ns . 'ontology/purpose/learning' ,'uri', 'uri'  );
			triple ($ns . 'ontology/purpose/learning', 'http://www.w3.org/2000/01/rdf-schema#label', 'Learning');
		}
		// "Purpose: Research",
		//if(d("Purpose: Research",$t))
		//literal ( $ID, $ns . 'ontology/purpose/research', (v("Purpose: Research",$line)== 'Y' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );
		if((v("Purpose: Research",$line)) === 'Y') 
		{	triple ($ID, 'http://www.w3.org/ns/oa#hasPurpose', $ns . 'ontology/purpose/research' ,'uri', 'uri'  );
			triple ($ns . 'ontology/purpose/research', 'http://www.w3.org/2000/01/rdf-schema#label', 'Research');
		}
		// if(d("Scope: Temporal",$t))
		// literal ( $ID, $ns . 'ontology/scope/temporal', (v("Scope: Temporal",$line) == 'Y' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );
		if((v("Scope: Temporal",$line)) === 'Y') 
		{	triple ($ID, 'http://www.w3.org/ns/oa#hasScope', $ns . 'ontology/scope/temporal' ,'uri', 'uri'  );
			triple ($ns . 'ontology/scope/temporal', 'http://www.w3.org/2000/01/rdf-schema#label', 'Temporal');
		}
		// if(d("Scope: Geographical",$t))
		// literal ( $ID, $ns . 'ontology/scope/geographical', (v("Scope: Geographical",$line) == 'Y' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );
		if((v("Scope: Geographical",$line)) === 'Y') 
		{	triple ($ID, 'http://www.w3.org/ns/oa#hasScope', $ns . 'ontology/scope/geographical' ,'uri', 'uri'  );
			triple ($ns . 'ontology/scope/geographical', 'http://www.w3.org/2000/01/rdf-schema#label', 'Geographical');
		}
		// if(d("Scope: Genre",$t))
		// literal ( $ID, $ns . 'ontology/scope/genre', (v("Scope: Genre",$line) == 'Y' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );
		if((v("Scope: Genre",$line)) === 'Y') 
		{	triple ($ID, 'http://www.w3.org/ns/oa#hasScope', $ns . 'ontology/scope/genre' ,'uri', 'uri'  );
			triple ($ns . 'ontology/scope/genre', 'http://www.w3.org/2000/01/rdf-schema#label', 'Genre');
		}
		// if(d("Scope: Artist",$t))
		// literal ( $ID, $ns . 'ontology/scope/artist', (v("Scope: Artist",$line) == 'Y' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );
		if((v("Scope: Artist",$line)) === 'Y') 
		{	triple ($ID, 'http://www.w3.org/ns/oa#hasScope', $ns . 'ontology/scope/artist' ,'uri', 'uri'  );
			triple ($ns . 'ontology/scope/artist', 'http://www.w3.org/2000/01/rdf-schema#label', 'Artist');
		}
		if(d("Scope: Formats",$t)){
			$fff = explode(',',v("Scope: Formats",$line ));
			foreach($fff as $f){
				triple ( $ID, $ns . 'ontology/scope/format', $ns . trim($f), 'uri', 'uri');
			}
		}
		if(d("Scope: MO type",$t))
		triple ( $ID, 'http://dbpedia.org/ontology/type', 'http://purl.org/ontology/mo/' . v("Scope: MO type",$line), 'uri', 'uri' ); 
		if(d("Scope: Object type",$t))
		{
			triple ( $ID, 'http://dbpedia.org/ontology/type', $ns . 'ontology/object/type/', md5(v("Scope: Object type",$line)), 'uri', 'uri' );
			triple ( md5(v("Scope: Object type",$line)), 'http://www.w3.org/2000/01/rdf-schema#label', v("Scope: Object type",$line));
		}
		
		// "Access: Public",
		//literal ( $ID, $ns . 'ontology/access/public', (v("Access: Public",$line)=='Y' ? 'true' : 'false'), 'http://www.w3.org/2001/XMLSchema#boolean' );
		if((v("Access: Public",$line)) === 'Y') 
		{	triple ($ID, 'http://purl.org/dc/terms/accessRights', $ns . 'ontology/access/public' ,'uri', 'uri'  );
			triple ($ns . 'ontology/access/public', 'http://www.w3.org/2000/01/rdf-schema#label', 'Public');
		}

		if((v("Access: Public",$line)) === 'N') 
		{	triple ($ID, 'http://purl.org/dc/terms/accessRights', $ns . 'ontology/access/restricted' ,'uri', 'uri'  );
			triple ($ns . 'ontology/access/restricted', 'http://www.w3.org/2000/01/rdf-schema#label', 'Restricted');
		}
		// "Access: license",
		triple ( $ID, $ns . 'http://purl.org/dc/terms/license', $ns . md5(v("Access: license",$line  )), 'uri', 'uri');
		triple ($ns . md5(v("Access: license",$line  )), 'http://www.w3.org/2000/01/rdf-schema#label', v("Access: license",$line  ));
		
		// "Access: Free/Charged",
		triple ( $ID, $ns . 'ontology/access/type', v("Access: Free/Charged", $line ) );

		if(d("Format: Interoperable?",$t))
		literal( $ID, $ns . 'ontology/format/interperable', (v("Format: Interoperable?",$line)=='Y'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		
		//if(d("Interface: Human consumption?",$t))
		//literal( $ID, $ns . 'ontology/interface/human', (v("Interface: Human consumption?",$line)=='Y'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if((v("Interface: Human consumption?",$line)) === 'Y') 
		{	triple ($ID, 'http://schema.org/featureList', $ns . 'ontology/feature/human-consumption' ,'uri', 'uri'  );
			triple ($ns . 'ontology/feature/human-consumption', 'http://www.w3.org/2000/01/rdf-schema#label', 'Human Consumption');
		}
		//if(d("Interface: API?",$t))
		//literal( $ID, $ns . 'ontology/interface/api', (v("Interface: API?",$line)=='Y'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if((v("Interface: API?",$line)) === 'Y') 
		{	triple ($ID, 'http://schema.org/featureList', $ns . 'ontology/feature/api' ,'uri', 'uri'  );
			triple ($ns . 'ontology/feature/api', 'http://www.w3.org/2000/01/rdf-schema#label', 'API');
		}
		// Interface: SPARQL endpoint?
		//if(d("Interface: SPARQL endpoint?",$t))
		//literal( $ID, $ns . 'ontology/interface/sparql', (v("Interface: SPARQL endpoint?",$line)=='Y'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if((v("Interface: SPARQL endpoint?",$line)) === 'Y') 
		{	triple ($ID, 'http://schema.org/featureList', $ns . 'ontology/feature/sparql-endpoint' ,'uri', 'uri'  );
			triple ($ns . 'ontology/feature/sparql-endpoint', 'http://www.w3.org/2000/01/rdf-schema#label', 'SPARQL Endpoint');
		}
		if((v("Interface: SPARQL endpoint?",$line)) === 'Y') 
			triple ($ID, 'http://rdfs.org/ns/void#sparqlEndpoint', v("SPARQL endpoint URI",$line),'uri', 'uri'  );

		// "Interface: Data Dump?",
		//if(d("Interface: Data Dump?",$t))
		//literal( $ID, $ns . 'ontology/interface/dump', (v("Interface: Data Dump?",$line)=='Y'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if((v("Interface: Data Dump?",$line)) === 'Y') 
		{	triple ($ID, 'http://schema.org/featureList', $ns . 'ontology/feature/data-dump' ,'uri', 'uri'  );
			triple ($ns . 'ontology/feature/data-dump', 'http://www.w3.org/2000/01/rdf-schema#label', 'Data Dump');
		}
		//if(d("Interface: Is it queryable?",$t))
		//literal( $ID, $ns . 'ontology/interface/queryable', (v("Interface: Is it queryable?",$line)=='Y'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if((v("Interface: Is it queryable?",$line)) === 'Y') 
		{	triple ($ID, 'http://schema.org/featureList', $ns . 'ontology/feature/query-interface' ,'uri', 'uri'  );
			triple ($ns . 'ontology/feature/query-interface', 'http://www.w3.org/2000/01/rdf-schema#label', 'Query Interface');
		}
		//if(d("Interface: Browsable?",$t))
		//literal( $ID, $ns . 'ontology/interface/browsable', (v("Interface: Browsable?",$line)=='Y'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if((v("Interface: Browsable?",$line)) === 'Y') 
		{	triple ($ID, 'http://schema.org/featureList', $ns . 'ontology/feature/browsing' ,'uri', 'uri'  );
			triple ($ns . 'ontology/feature/browsing', 'http://www.w3.org/2000/01/rdf-schema#label', 'Browsing');
		}
		if(d("Collection: Size",$t))
		{	triple( $ID, 'http://purl.org/dc/terms/extent', $ns . md5((v("Collection: Size",$line))),'uri','uri');
			triple($ns . md5((v("Collection: Size",$line))), 'http://www.w3.org/2000/01/rdf-schema#label', v("Collection: Size",$line));
		}
		if(d("Data size",$t))
		triple( $ID, 'http://www.w3.org/ns/dcat#byteSize', (v("Data size",$line)));
		
		if(d("Symbolic: Machine readable?",$t))
		literal( $ID, $ns . 'ontology/symbolic/machineReadable', (v("Symbolic: Machine readable?",$line)=='Y'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );

		// Features
		//if(d("Feature: Melody",$t))
		//literal( $ID, $ns . 'ontology/feature/melody', (v("Feature: Melody",$line)=='Y'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if((v("Feature: Melody",$line)) === 'Y') 
		{	triple ($ID, 'http://xmlns.com/foaf/0.1/primaryTopic', $ns . 'ontology/feature/melody' ,'uri', 'uri'  );
			triple ($ns . 'ontology/feature/melody', 'http://www.w3.org/2000/01/rdf-schema#label', 'Melody');
		}
		//if(d("Feature: Harmony",$t))
		//literal( $ID, $ns . 'ontology/feature/harmony', (v("Feature: Harmony",$line)=='Y'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if((v("Feature: Harmony",$line)) === 'Y') 
		{	triple ($ID, 'http://xmlns.com/foaf/0.1/primaryTopic', $ns . 'ontology/feature/harmony' ,'uri', 'uri'  );
			triple ($ns . 'ontology/feature/harmony', 'http://www.w3.org/2000/01/rdf-schema#label', 'Harmony');
		}
		//if(d("Feature: Lyrics",$t))
		//literal( $ID, $ns . 'ontology/feature/lyrics', (v("Feature: Lyrics",$line)=='Y'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if((v("Feature: Lyrics",$line)) === 'Y') 
		{	triple ($ID, 'http://xmlns.com/foaf/0.1/primaryTopic', $ns . 'ontology/feature/lyrics' ,'uri', 'uri'  );
			triple ($ns . 'ontology/feature/lyrics', 'http://www.w3.org/2000/01/rdf-schema#label', 'Lyrics');
		}
		//if(d("Feature: Rhythm",$t))
		//literal( $ID, $ns . 'ontology/feature/rhythm', (v("Feature: Rhythm",$line)=='Y'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if((v("Feature: Rhythm",$line)) === 'Y') 
		{	triple ($ID, 'http://xmlns.com/foaf/0.1/primaryTopic', $ns . 'ontology/feature/rhythm' ,'uri', 'uri'  );
			triple ($ns . 'ontology/feature/rhythm', 'http://www.w3.org/2000/01/rdf-schema#label', 'Rhythm');
		}
		//if(d("Feature: Timbre",$t))
		//literal( $ID, $ns . 'ontology/feature/timbre', (v("Feature: Timbre",$line)=='Y'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if((v("Feature: Timbre",$line)) === 'Y') 
		{	triple ($ID, 'http://xmlns.com/foaf/0.1/primaryTopic', $ns . 'ontology/feature/timbre' ,'uri', 'uri'  );
			triple ($ns . 'ontology/feature/timbre', 'http://www.w3.org/2000/01/rdf-schema#label', 'Timbre');
		}
		//if(d("Feature: Contour/Shape",$t))
		//literal( $ID, $ns . 'ontology/feature/shape', (v("Feature: Contour/Shape",$line)=='Y'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if((v("Feature: Contour/Shape",$line)) === 'Y') 
		{	triple ($ID, 'http://xmlns.com/foaf/0.1/primaryTopic', $ns . 'ontology/feature/contour-or-shape' ,'uri', 'uri'  );
			triple ($ns . 'ontology/feature/contour-or-shape', 'http://www.w3.org/2000/01/rdf-schema#label', 'Contour or Shape');
		}
		//if(d("Feature: Structure",$t))
		// literal( $ID, $ns . 'ontology/feature/structure', (v("Feature: Structure",$line)=='Y'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if((v("Feature: Structure",$line)) === 'Y') 
		{	triple ($ID, 'http://xmlns.com/foaf/0.1/primaryTopic', $ns . 'ontology/feature/structure' ,'uri', 'uri'  );
			triple ($ns . 'ontology/feature/structure', 'http://www.w3.org/2000/01/rdf-schema#label', 'Structure of a song');
		}
		//if(d("Feature: Descriptive Metadata",$t))
		//literal( $ID, $ns . 'ontology/feature/metadata', (v("Feature: Descriptive Metadata",$line)=='Y'?'true':'false'),'http://www.w3.org/2001/XMLSchema#boolean' );
		if((v("Feature: Descriptive Metadata",$line)) === 'Y') 
		{	triple ($ID, 'http://xmlns.com/foaf/0.1/primaryTopic', $ns . 'ontology/feature/descriptive-metadata' ,'uri', 'uri'  );
			triple ($ns . 'ontology/feature/descriptive-metadata', 'http://www.w3.org/2000/01/rdf-schema#label', 'Descriptive Metadata');
		}
		// "Situation/Task"
		if(d("Situation/Task",$t)){
			$sss = explode(';', v("Situation/Task", $line));
			foreach($sss as $s){
				triple( $ID, $ns . 'ontology/situation/task', $ns . md5(trim($s)), 'uri', 'uri');
				triple($ns . md5(trim($s)), 'http://www.w3.org/2000/01/rdf-schema#label', trim($s));
			}
		}

		// "Target audience"
		if(d("Target audience",$t)){
			$aaa = explode(';', v("Target audience", $line));
			foreach($aaa as $a){
				triple( $ID, 'http://purl.org/dc/terms/audience', $ns . md5(trim($a)), 'uri', 'uri');
				triple($ns . md5(trim($a)), 'http://www.w3.org/2000/01/rdf-schema#label', trim($a));
			}
		}
	}
	
	fclose ( $handle );
} else {
	// error opening the file.
}

$ser = ARC2::getNtriplesSerializer ();
header ( 'Content-Type:', 'text/plain:charset=utf-8', 200 );
print $ser->getSerializedTriples ( $triples );
