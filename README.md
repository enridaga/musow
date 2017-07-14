
# MuDOW: Musical Data on the web

MuDOW is a survey on musical data available on the web and a RDF dataset including results of the survey. 
Here you can find the script to generate the RDF dataset from the managed CSV and several SPARQL queries.

MuDOW RDF dataset can be queried at [https://data.open.ac.uk/sparql](https://data.open.ac.uk/sparql). Here an example resource for starting browsing the dataset: [MIDI Linked Dataset](http://data.open.ac.uk/mudow/2c52e5179258305c74fcc637615eb123). 

# A guide for querying MuDOW with SPARQL

The following selection of SPARQL queries is meant to be a useful guide to the user who wants to discover  musical data available on the web and how to reuse it! 

## Table of contents

* [General queries](#general-queries)
* [Catalogs](#catalogs)
* [Digital libraries and repositories](#digital-libraries-and-repositories)
* [Datasets](#datasets)
* [Digital editions](#digital-editions)
* [Services and Sofwares](#services-and-softwares)
* [Schemas and ontologies](#schemas-and-ontologies)
* [Formats](#formats)
* [About symbolic notation](#about-symbolic-notation)

## General queries

The following examples show an overview of resources gathered and described in MuDOW survey.

**GQ1.** How many resources have been included in the survey?

<pre>
SELECT (COUNT(?s) as ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?s &lt;http://xmlns.com/foaf/0.1/homepage&gt; ?o .
}
</pre> 

**GQ2.** Which types of resource have been included in the survey?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?label (count(DISTINCT ?resource) AS ?count) 
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   	?resource <http://purl.org/spar/datacite/hasGeneralResourceType> ?type .
   	?type rdfs:label ?label .
}

GROUP BY ?label ?count
ORDER BY desc(?count)
</pre> 

**GQ3.** Which criteria have been used to gather such resources? How many resources have been gathered by means of a criterion?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT DISTINCT ?searchCriterion ?label (count(?s) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?s &lt;http://purl.org/spar/fabio/hasSubjectTerm&gt; ?searchCriterion .
   ?searchCriterion rdfs:label ?label .
}
GROUP BY ?searchCriterion ?label
ORDER BY desc(?count)
</pre> 

**GQ4.** What is the extent of gathered resources? 

Results refer to the number of musical items collected by the described resources. When a Linked Dataset is described, the extent refers to the number of RDF triples.

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?label (COUNT(?project) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
  ?project &lt;http://purl.org/dc/terms/extent&gt; ?size .
  ?size rdfs:label ?label .
}
GROUP BY ?label
ORDER BY ?label
</pre> 

**GQ5.** What is the main data source of gathered resources? 

Results refer to the original musical object described in the resource i.e. audio files, metadata or symbolic notations.

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?categoryLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
  ?resource &lt;http://dbpedia.org/ontology/category&gt; ?category .
  ?category rdfs:label ?categoryLabel .
}
GROUP BY ?categoryLabel
ORDER BY ?categoryLabel
</pre> 

**GQ6.** What is the target audience of gathered resources? 

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT (count(DISTINCT ?resource) AS ?count) ?audience
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
  ?resource &lt;http://purl.org/dc/terms/audience&gt; ?typeaudience .
  ?typeaudience rdfs:label ?audience .
}
GROUP BY ?audience
ORDER BY ?count
</pre> 

**GQ7.** In which kind of resources are researchers interested? 

Results refer to resources that provide audio files, metadata or symbolic notation, as described in GQ5.

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?categoryLabel (count(DISTINCT ?resource) AS ?count) 
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
  ?resource &lt;http://purl.org/dc/terms/audience&gt; ?typeaudience ; 
  			&lt;http://dbpedia.org/ontology/category&gt; ?category .
  ?category rdfs:label ?categoryLabel .
  ?typeaudience rdfs:label ?audience .
  filter(?audience="researchers") .
}
GROUP BY ?categoryLabel ?count
ORDER BY ?count
</pre> 

**GQ8.** How many resources are useful to both researchers and performers? 

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT (count(DISTINCT ?resource) AS ?count) 
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
  	?resource &lt;http://purl.org/dc/terms/audience&gt; ?typeaudience .
  	?resource &lt;http://purl.org/dc/terms/audience&gt; ?typeaudience2 .
   	?typeaudience rdfs:label ?audience .
  	?typeaudience2 rdfs:label ?audience2 .
    filter(?audience="researchers" ) . filter(?audience2="performers").
}
GROUP BY ?audience
ORDER BY ?count
</pre> 

**GQ9.** How many resources are not targeted on researchers? 

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT (count(DISTINCT ?resource) AS ?count) 
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
    ?resource &lt;http://purl.org/dc/terms/audience&gt; ?typeaudience .
    ?typeaudience rdfs:label ?audience .
    FILTER(?audience NOT IN ('researchers')).
}
</pre> 

## Catalogs

Among the resources gathered in the survey there are catalogs of online resources for musicologists, musicians and researchers in general. These ones have been used as sources for retrieving most of of the resources described in MuDOW.

**CT1.** How many resources have been gathered by means of catalogs? 

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?catalogueLabel (count(?resource) AS ?count)  
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
  ?catalogue rdfs:label ?catalogueLabel ; &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?catalogueType .
  ?catalogueType rdfs:label ?typeLabel .
  filter(?typeLabel="Catalogue") .
  ?resource &lt;http://purl.org/spar/fabio/hasSubjectTerm&gt; ?catalogue
}
</pre>

**CT2.** What's the focus of resources gathered by means of catalogs?

Results refer to the original sources analysed by the collected resources, i.e. audio files, notated music or simply cataloguing metadata.

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?categoryLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
  ?catalogue rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?catalogueType .
  ?catalogueType rdfs:label ?typeLabel . filter(?typeLabel="Catalogue") .
  ?resource &lt;http://purl.org/spar/fabio/hasSubjectTerm&gt; ?catalogue ; 
            &lt;http://dbpedia.org/ontology/category&gt; ?category .
  ?category rdfs:label ?categoryLabel .
}
GROUP BY ?categoryLabel ?count
ORDER BY ?count
</pre>

**CT3.** What type of resources gathered by means of catalogs? 

e.g. datasets, repositories, digital editions. 

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?resourceTypeLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?catalogue rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?catalogueType .
   ?catalogueType rdfs:label ?typeLabel . filter(?typeLabel="Catalogue") .
   ?resource &lt;http://purl.org/spar/fabio/hasSubjectTerm&gt; ?catalogue ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?type .
   ?type rdfs:label ?resourceTypeLabel .
}
GROUP BY ?categoryLabel ?count
ORDER BY ?count
</pre>

**CT4.** What is the target audience of resources gathered by means of catalogs? 

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT (count(DISTINCT ?resource) AS ?count) ?audience
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?catalogue rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?catalogueType .
   ?catalogueType rdfs:label ?typeLabel . filter(?typeLabel="Catalogue") .
   ?resource &lt;http://purl.org/spar/fabio/hasSubjectTerm&gt; ?catalogue ; 
             &lt;http://purl.org/dc/terms/audience&gt; ?typeaudience .
   ?typeaudience rdfs:label ?audience .
}
GROUP BY ?audience
ORDER BY ?count
</pre>

**CT5.** Considering resources gathered by catalogs targeted on researchers, performers and scholars, what is their extent?

Results refer to the estimated number of items collected by each resource.

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT (count(DISTINCT ?resource) AS ?count) ?extent
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
    ?resource &lt;http://purl.org/dc/terms/audience&gt; ?typeaudience ; 
              &lt;http://purl.org/dc/terms/audience&gt; ?typeaudience2 ; 
              &lt;http://purl.org/dc/terms/audience&gt; ?typeaudience3 ;
              &lt;http://purl.org/dc/terms/extent&gt; ?extenttype .
    ?extenttype rdfs:label ?extent .
    ?typeaudience rdfs:label ?audience .
    ?typeaudience2 rdfs:label ?audience2 .
    ?typeaudience3 rdfs:label ?audience3 .
    filter(?audience="researchers" ) . filter(?audience2="performers"). filter(?audience3="scholars").
}
GROUP BY ?extent
ORDER BY ?extent
</pre>

**CT6.** Which catalogs, and how many times, are reused in other projects?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?resourceLabel (count(DISTINCT ?otherResource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?resourceLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://www.w3.org/2004/02/skos/core#related&gt; ?otherResource .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel='Catalogue') .
}
GROUP BY ?resourceLabel ?count
ORDER BY DESC(?count)
</pre>

## Digital libraries and repositories

We considere digital libraries as repositories of heterogeneous materials, meaning that different cultural objects are described - e.g. books, photographs, artworks, music scores. While repositories gather materials about one type of object, also providing different media (e.g. digitized scores, audio files, trascriptions of melody).

**DR1.** How may repositories and digital libraries are found?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel IN ("Repository", "Digital library")) .
}

</pre>

**DR2.** What is the scale of repositories and digital libraries?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#>
SELECT ?extentLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://purl.org/dc/terms/extent&gt; ?extent .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel IN ("Repository", "Digital library")) .
   ?extent rdfs:label ?extentLabel .
}
GROUP BY ?extentLabel ?count
ORDER BY ?count
</pre>

**DR3.** Which are the most/least used data formats? 

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT DISTINCT ?formatLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://data.open.ac.uk/mudow/ontology/scope/format&gt; ?format .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel IN ("Repository", "Digital library")) .
   ?format rdfs:label ?formatLabel .
}
GROUP BY ?formatLabel ?count
ORDER BY DESC(?count)
</pre>

**DR4.** What are the most/least used formats compared to scale?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT DISTINCT ?extentLabel ?formatLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://purl.org/dc/terms/extent&gt; ?extent ; 
             &lt;http://data.open.ac.uk/mudow/ontology/scope/format&gt; ?format .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel IN ("Repository", "Digital library")) .
   ?extent rdfs:label ?extentLabel . ?format rdfs:label ?formatLabel .
}
GROUP BY ?extentLabel ?formatLabel ?count
ORDER BY ?extentLabel DESC(?count)
</pre>

**DR5.** How many use an interoperable/standard format?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://schema.org/featureList&gt; 
                  &lt;http://data.open.ac.uk/mudow/ontology/feature/interoperable&gt; .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel IN ("Repository", "Digital library")) .
}
</pre>

**DR6.** How many of them provide digitizations of scores or songs?

Results refere to a combination of image formats that are generally used to publish music contents. 

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT DISTINCT (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://data.open.ac.uk/mudow/ontology/scope/format&gt; ?format .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel IN ("Repository", "Digital library")) .
   filter( str(?format) IN ("http://data.open.ac.uk/mudow/pdf" , "http://data.open.ac.uk/mudow/jpg", 
    "http://data.open.ac.uk/mudow/tiff", "http://data.open.ac.uk/mudow/iiif", "http://data.open.ac.uk/mudow/gif", "http://data.open.ac.uk/mudow/png", "http://data.open.ac.uk/mudow/djvu"))
}
</pre>

**DR7.** How many of them provide audio files?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT DISTINCT (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://data.open.ac.uk/mudow/ontology/scope/format&gt; ?format .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel IN ("Repository", "Digital library")) .
   filter( str(?format) IN ("http://data.open.ac.uk/mudow/midi" , "http://data.open.ac.uk/mudow/mp3", 
    "http://data.open.ac.uk/mudow/audio", "http://data.open.ac.uk/mudow/video", "http://data.open.ac.uk/mudow/finale", "http://data.open.ac.uk/mudow/sibelius", "http://data.open.ac.uk/mudow/flac"))
}
</pre>

**DR8.** How many of them provide structured data about notated music?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT DISTINCT (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://data.open.ac.uk/mudow/ontology/scope/format&gt; ?format .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel IN ("Repository", "Digital library")) .
   filter( str(?format) IN ("http://data.open.ac.uk/mudow/musicxml" , "http://data.open.ac.uk/mudow/xml", 
    "http://data.open.ac.uk/mudow/rdf", "http://data.open.ac.uk/mudow/mei/xml", "http://data.open.ac.uk/mudow/cap/xml", "http://data.open.ac.uk/mudow/humdrum", "http://data.open.ac.uk/mudow/kern", "http://data.open.ac.uk/mudow/lilypond", "http://data.open.ac.uk/mudow/musedata", "http://data.open.ac.uk/mudow/musescore", "http://data.open.ac.uk/mudow/myr", "http://data.open.ac.uk/mudow/noteworthy", "http://data.open.ac.uk/mudow/py", "http://data.open.ac.uk/mudow/ram", "http://data.open.ac.uk/mudow/mu2"))
}
</pre>

**DR9.** What is the scope of musical data in repositories and digital libraries?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?scopeLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://www.w3.org/ns/oa#hasScope&gt; ?scope .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel IN ("Repository", "Digital library")) .
   ?scope rdfs:label ?scopeLabel .
}
GROUP BY ?scopeLabel ?count
ORDER BY DESC(?count)
</pre>

**DR10.** What is the relation between scope and scale?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?extentLabel ?scopeLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://purl.org/dc/terms/extent&gt; ?extent ; 
             &lt;http://www.w3.org/ns/oa#hasScope&gt; ?scope .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel IN ("Repository", "Digital library")) .
   ?scope rdfs:label ?scopeLabel . ?extent rdfs:label ?extentLabel .
}
GROUP BY ?scopeLabel ?extentLabel ?count
ORDER BY ?extentLabel DESC(?count)
</pre>

**DR11.** What is the relation between scope and formats?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?scopeLabel ?formatLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?resourceLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://www.w3.org/ns/oa#hasScope&gt; ?scope ; 
             &lt;http://data.open.ac.uk/mudow/ontology/scope/format&gt; ?format .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel IN ("Repository", "Digital library")) .
   ?format rdfs:label ?formatLabel .
   ?scope rdfs:label ?scopeLabel .
}
GROUP BY ?scopeLabel ?formatLabel ?count
ORDER BY ?scopeLabel DESC(?count)
</pre>

**DR12.** What is the relation between scope and machine readable data about symbolic notation?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?scopeLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?resourceLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://www.w3.org/ns/oa#hasScope&gt; ?scope ; 
             &lt;http://schema.org/featureList&gt; 
                &lt;http://data.open.ac.uk/mudow/ontology/feature/symbolic-machine-readable&gt; .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel IN ("Repository", "Digital library")) .
   ?scope rdfs:label ?scopeLabel .
}
GROUP BY ?scopeLabel ?count
ORDER BY ?scopeLabel DESC(?count)
</pre>

**DR13.** What is the relation between the provision of structured data on symbolic notation and scale?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?extentLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?resourceLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://purl.org/dc/terms/extent&gt; ?extent ; 
             &lt;http://schema.org/featureList&gt; 
                  &lt;http://data.open.ac.uk/mudow/ontology/feature/symbolic-machine-readable&gt; .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel IN ("Repository", "Digital library")) .
   ?scope rdfs:label ?scopeLabel . ?extent rdfs:label ?extentLabel
} 
GROUP BY ?extentLabel ?count
ORDER BY ?extentLabel DESC(?count)
</pre>

**DR14.** Which features of symbolic notation are represented in repositories?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?featureLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?resourceLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://xmlns.com/foaf/0.1/primaryTopic&gt; ?feature .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel IN ("Repository", "Digital library")) .
   ?feature rdfs:label ?featureLabel .
}
GROUP BY ?featureLabel ?count
ORDER BY DESC(?count)
</pre>

**DR15.** Do the sources collected in digital libraries and repositories are audio tracks, symbolic notation or metadata?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?categoryLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://dbpedia.org/ontology/category&gt; ?category .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel IN ("Repository", "Digital library")) .
   ?category rdfs:label ?categoryLabel .
}
GROUP BY ?categoryLabel ?count
</pre>

**DR16.** How many repositories are free of charge? 

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://data.open.ac.uk/mudow/ontology/access/type&gt; "Free" . 
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel IN ("Repository", "Digital library")) .
}
</pre>

**DR17.** Is this content properly licensed? What are the licences used?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?licenseLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://purl.org/dc/terms/license&gt; ?license .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel IN ("Repository", "Digital library")) .
   ?license rdfs:label ?licenseLabel .
}
GROUP BY ?licenseLabel ?count
ORDER BY DESC(?count)
</pre>

**DR18.** What Is the audience of such resources?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?audience (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://purl.org/dc/terms/audience&gt; ?typeaudience .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel IN ("Repository", "Digital library")) .
   ?typeaudience rdfs:label ?audience . 
}
GROUP BY ?audience ?count
ORDER BY DESC(?count)
</pre>

**DR19.** What is the purpose of such resources? Learning or research? 

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?purposeLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://www.w3.org/ns/oa#hasPurpose&gt; ?purpose .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel IN ("Repository", "Digital library")) .
   ?purpose rdfs:label ?purposeLabel .
}
GROUP BY ?purposeLabel ?count
ORDER BY DESC(?count)
</pre>

**DR20.** What is the purpose of such resources? Entertainment? 

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://purl.org/dc/terms/audience&gt; ?typeaudience .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel IN ("Repository", "Digital library")) .
   ?typeaudience rdfs:label ?audience . filter(?audience IN ("amateurs", "listeners")) .
}
</pre>

**DR21.** Is data published in repositories or digital libraries reused in other projects?
 
<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?resourceLabel (count(DISTINCT ?otherResource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?resourceLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel IN ("Repository", "Digital library")) .
   ?resource ^&lt;http://www.w3.org/2004/02/skos/core#related&gt; ?otherResource .
}
GROUP BY ?resourceLabel ?count
ORDER BY DESC(?count)
</pre>

## Datasets

**DS1.** How many datasets on musical data are available on the web?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel='Dataset') .
}
</pre>

**DS2.** In which formats are datasets released?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT DISTINCT ?formatLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://data.open.ac.uk/mudow/ontology/scope/format&gt; ?format .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel='Dataset') .
   ?format rdfs:label ?formatLabel .
}
GROUP BY ?formatLabel ?count
ORDER BY DESC(?count)
</pre>

**DS3.** What are the most/least used data formats compared to the scale?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT DISTINCT ?extentLabel ?formatLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://purl.org/dc/terms/extent&gt; ?extent ; 
             &lt;http://data.open.ac.uk/mudow/ontology/scope/format&gt; ?format .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel='Dataset') .
   ?extent rdfs:label ?extentLabel . ?format rdfs:label ?formatLabel .
}
GROUP BY ?extentLabel ?formatLabel ?count
ORDER BY ?extentLabel DESC(?count)
</pre>

**DS4.** What is the scope of datasets?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?scopeLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://www.w3.org/ns/oa#hasScope&gt; ?scope .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel='Dataset') .
   ?scope rdfs:label ?scopeLabel .
}
GROUP BY ?scopeLabel ?count
ORDER BY DESC(?count)
</pre>

**DS5.** Which features of notated music are represented?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?featureLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?resourceLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://xmlns.com/foaf/0.1/primaryTopic&gt; ?feature .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel='Dataset') .
   ?feature rdfs:label ?featureLabel .
}

GROUP BY ?featureLabel ?count
ORDER BY DESC(?count)
</pre>

**DS6.** Does data represent features extracted from audio files, metadata or notated music?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?categoryLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?resourceLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://xmlns.com/foaf/0.1/primaryTopic&gt; ?feature ; 
             &lt;http://dbpedia.org/ontology/category&gt; ?category .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel='Dataset') .
   ?category rdfs:label ?categoryLabel .
   ?feature rdfs:label ?featureLabel .
}
GROUP BY ?categoryLabel ?count
ORDER BY DESC(?count)
</pre>

**DS7.** Which services are offered to access data? 

e.g. API, SPARQL endpoint, queryable interfaces

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?feautureListLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?resourceLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://xmlns.com/foaf/0.1/primaryTopic&gt; ?feature ;
             &lt;http://schema.org/featureList&gt; ?feautureList .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel='Dataset') .
   ?feautureList rdfs:label ?feautureListLabel .
   ?feature rdfs:label ?featureLabel .
}
GROUP BY ?feautureListLabel ?count
ORDER BY DESC(?count)
</pre>

**DS8.** What is the task or situation in which they claim to be useful?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?taskLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?resourceLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://data.open.ac.uk/mudow/ontology/situation/task&gt; ?task .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel='Dataset') .
   ?task rdfs:label ?taskLabel .
}
GROUP BY ?taskLabel ?count
ORDER BY DESC(?count)
</pre>

**DS9.** Is this content properly licensed? Which licences are used?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?licenseLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://purl.org/dc/terms/license&gt; ?license .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel='Dataset') .
   ?license rdfs:label ?licenseLabel
}
GROUP BY ?licenseLabel ?count
ORDER BY DESC(?count)
</pre>

**DS10.** What is the purpose? Learning or research? 

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?purposeLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://www.w3.org/ns/oa#hasPurpose&gt; ?purpose .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel='Dataset') .
   ?purpose rdfs:label ?purposeLabel .
}
GROUP BY ?purposeLabel ?count
ORDER BY DESC(?count)
</pre>

**DS11.** What is the purpose? Entertainment?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://purl.org/dc/terms/audience&gt; ?typeaudience .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel='Dataset') .
   ?typeaudience rdfs:label ?audience . filter(?audience IN ("amateurs", "listeners")) .
}
</pre>

**DS12.** Is data reused in other projects?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?resourceLabel (count(DISTINCT ?otherResource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?resourceLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             ^&lt;http://www.w3.org/2004/02/skos/core#related&gt; ?otherResource .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel='Dataset') .
}

GROUP BY ?resourceLabel ?count
ORDER BY DESC(?count)
</pre>

**DS13.** What’s the main source of LOD on music? Metadata, media or audio? 

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?categoryLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?resourceLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://xmlns.com/foaf/0.1/primaryTopic&gt; ?feature ;
             &lt;http://dbpedia.org/ontology/category&gt; ?category ;
             &lt;http://data.open.ac.uk/mudow/ontology/scope/format&gt; ?format .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel='Dataset') .
   ?format rdfs:label ?formatLabel . filter(?formatLabel='RDF')
   ?category rdfs:label ?categoryLabel .
   ?feature rdfs:label ?featureLabel .
}
GROUP BY ?categoryLabel ?count
ORDER BY DESC(?count)
</pre>

**DS14.** Which services they provide?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?feautureListLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?resourceLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://schema.org/featureList&gt; ?feautureList ;
             &lt;http://data.open.ac.uk/mudow/ontology/scope/format&gt; ?format .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel='Dataset') .
   ?feautureList rdfs:label ?feautureListLabel .
   ?format rdfs:label ?formatLabel . filter(?formatLabel='RDF') .
}
GROUP BY ?feautureListLabel ?count
ORDER BY DESC(?count)
</pre>

**DS15.** Which tasks or situations they address?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?taskLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?resourceLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://data.open.ac.uk/mudow/ontology/scope/format&gt; ?format ;
             &lt;http://data.open.ac.uk/mudow/ontology/situation/task&gt; ?task .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel='Dataset') .
   ?task rdfs:label ?taskLabel .
   ?format rdfs:label ?formatLabel . filter(?formatLabel='RDF').
}
GROUP BY ?taskLabel ?count
ORDER BY DESC(?count)
</pre>

## Digital editions

**DE1.** How many digital editions on muscial contents are available?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel='Digital edition') .
}
</pre>

**DE2.** Which formats are mainly used?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT DISTINCT ?formatLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://data.open.ac.uk/mudow/ontology/scope/format&gt; ?format .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel='Digital edition') .
   ?format rdfs:label ?formatLabel .
}
GROUP BY ?formatLabel ?count
ORDER BY DESC(?count)
</pre>

**DE3.** What is their scope?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?scopeLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://www.w3.org/ns/oa#hasScope&gt; ?scope .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel='Digital edition') .
   ?scope rdfs:label ?scopeLabel .
}
GROUP BY ?scopeLabel ?count
ORDER BY DESC(?count)
</pre>

**DE4.** What is tehir scale?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT  ?extentLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://purl.org/dc/terms/extent&gt; ?extent. 
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel='Digital edition') .
   ?extent rdfs:label ?extentLabel .
}
GROUP BY ?extentLabel ?count
ORDER BY ?extentLabel DESC(?count)  
</pre>

**DE5.** Do they offer playable objects?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://schema.org/featureList&gt; 
                &lt;http://data.open.ac.uk/mudow/ontology/feature/playable&gt; . 
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel='Digital edition') .
}
</pre>

**DE6.** Which musical features are represented?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?featureLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?resourceLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://xmlns.com/foaf/0.1/primaryTopic&gt; ?feature .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel='Digital edition') .
   ?feature rdfs:label ?featureLabel .
}
GROUP BY ?featureLabel ?count
ORDER BY DESC(?count) ?featureLabel
</pre>

**DE7.** Do they represent features extracted from audio files, metadata or notated music?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?categoryLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://dbpedia.org/ontology/category&gt; ?category .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel='Digital edition') .
   ?category rdfs:label ?categoryLabel .
}
GROUP BY ?categoryLabel ?count
</pre>

**DE8.** Which services are offered to access data? 

E.g. API, SPARQL endpoint

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?feautureListLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?resourceLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://xmlns.com/foaf/0.1/primaryTopic&gt; ?feature ;
             &lt;http://schema.org/featureList&gt; ?feautureList .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel='Digital edition') .
   ?feautureList rdfs:label ?feautureListLabel .
   ?feature rdfs:label ?featureLabel .
}
GROUP BY ?feautureListLabel ?count
ORDER BY DESC(?count)
</pre>

**DE9.** Which are the claimed tasks of digital editions?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?taskLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?resourceLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://data.open.ac.uk/mudow/ontology/situation/task&gt; ?task .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel='Digital edition') .
   ?task rdfs:label ?taskLabel .
}
GROUP BY ?taskLabel ?count
ORDER BY DESC(?count)
</pre>

**DE10.** Are data reused in other projects?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?resourceLabel (count(DISTINCT ?otherResource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?resourceLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             ^&lt;http://www.w3.org/2004/02/skos/core#related&gt; ?otherResource .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel='Digital edition') .
}
GROUP BY ?resourceLabel ?count
ORDER BY DESC(?count)
</pre>

**DE11.** Which resources are mainly reused by these projects?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?otherLabel (count(DISTINCT ?otherResource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?resourceLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://www.w3.org/2004/02/skos/core#related&gt; ?otherResource .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel='Digital edition') .
   ?otherResource rdfs:label ?otherLabel
}
GROUP BY ?otherLabel ?count
ORDER BY DESC(?count)
</pre>

**DE12.** In which situation/task are resources reused in digital editions?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT DISTINCT ?otherLabel ?taskLabel
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?resourceLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://data.open.ac.uk/mudow/ontology/situation/task&gt; ?task ; 
             &lt;http://www.w3.org/2004/02/skos/core#related&gt; ?otherResource . 
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel='Digital edition') .
   ?task rdfs:label ?taskLabel . 
   ?otherResource rdfs:label ?otherLabel
}
GROUP BY ?otherLabel ?taskLabel
ORDER BY ?otherLabel
</pre>

## Services and Sofwares

**SS1.** How many software and services are available on the web?

The focus of the survey is on data rather than SW, thus results might not be exhaustive. Moreover, results refer to resources that have been either reused or mentioned by projects included in the survey.

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType .
  ?resourceType rdfs:label ?typeLabel . filter(?typeLabel in ('Software', 'Service')) .
}
</pre>

**SS2.** Which standards/formats do they use?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT DISTINCT ?formatLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://data.open.ac.uk/mudow/ontology/scope/format&gt; ?format .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel in ('Software', 'Service')) .
   ?format rdfs:label ?formatLabel .
}
GROUP BY ?formatLabel ?count
ORDER BY DESC(?count)
</pre>

**SS3.** In which tasks or situations they are applied?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?taskLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?resourceLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://data.open.ac.uk/mudow/ontology/situation/task&gt; ?task .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel in ('Service', "Software")) .
   ?task rdfs:label ?taskLabel .
}
GROUP BY ?taskLabel ?count
ORDER BY DESC(?count)
</pre>

**SS4.** Which features they deal with?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?featureLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?resourceLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://xmlns.com/foaf/0.1/primaryTopic&gt; ?feature .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel in ('Service', "Software")) .
   ?feature rdfs:label ?featureLabel .
}
GROUP BY ?featureLabel ?count
ORDER BY DESC(?count) ?featureLabel
</pre>

**SS5.** Which sources they use to extract features?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?categoryLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://dbpedia.org/ontology/category&gt; ?category .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel in ('Service', "Software")) .
   ?category rdfs:label ?categoryLabel .
}
GROUP BY ?categoryLabel ?count
ORDER BY desc(?count)
</pre>

**SS6.** Which resources they reuse?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?otherLabel (count(DISTINCT ?otherResource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?resourceLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://www.w3.org/2004/02/skos/core#related&gt; ?otherResource .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel in ('Service', "Software")) .
   ?otherResource rdfs:label ?otherLabel
}
GROUP BY ?otherLabel ?count
ORDER BY DESC(?count)
</pre>

**SS7.** Which resources reuse them?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?resourceLabel ?otherLabel (count(DISTINCT ?otherResource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?resourceLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             ^&lt;http://www.w3.org/2004/02/skos/core#related&gt; ?otherResource .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel in ('Service', "Software")) .
   ?otherResource rdfs:label ?otherLabel
}
GROUP BY ?resourceLabel ?otherLabel ?count
ORDER BY ?resourceLabel
</pre>

## Schemas and ontologies

**SO1.** How many ontologies and schemas are availabel for describing music domain?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType .
  ?resourceType rdfs:label ?typeLabel . filter(?typeLabel in ('Ontology', 'Schema')) .
}
</pre>

**SO2.** Which sources they consider in describin musical features?

i.e. audio files, metadata or notated music .

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?categoryLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://dbpedia.org/ontology/category&gt; ?category .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel in ('Ontology', "Schema")) .
   ?category rdfs:label ?categoryLabel .
}
GROUP BY ?categoryLabel ?count
ORDER BY desc(?count)
</pre>

**SO3.** Which features are best described by means of them? 

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?featureLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?resourceLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://xmlns.com/foaf/0.1/primaryTopic&gt; ?feature .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel in ('Ontology', "Schema")) .
   ?feature rdfs:label ?featureLabel .
}
GROUP BY ?featureLabel ?count
ORDER BY DESC(?count) ?featureLabel
</pre>

**SO4.** How many projects reuse them? Which one?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?resourceLabel ?otherLabel (count(DISTINCT ?otherResource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?resourceLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             ^&lt;http://www.w3.org/2004/02/skos/core#related&gt; ?otherResource .
   ?resourceType rdfs:label ?typeLabel . filter(?typeLabel in ('Schema', "Ontology")) .
   ?otherResource rdfs:label ?otherLabel
}
GROUP BY ?resourceLabel ?otherLabel ?count
ORDER BY ?resourceLabel
</pre>

## Formats

**FO1.** Which formats are curently used in music domain? How many? And how many times have been used?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT DISTINCT ?formatLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://data.open.ac.uk/mudow/ontology/scope/format&gt; ?format .
   ?resourceType rdfs:label ?typeLabel . 
   ?format rdfs:label ?formatLabel .
}
GROUP BY ?formatLabel ?count
ORDER BY DESC(?count)
</pre>

**FO2.** Are they interoperable or used in contexts where data are also provided in interoperable ways?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT DISTINCT ?formatLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://data.open.ac.uk/mudow/ontology/scope/format&gt; ?format ; 
             &lt;http://schema.org/featureList&gt; 
                  &lt;http://data.open.ac.uk/mudow/ontology/feature/interoperable&gt; .
   ?resourceType rdfs:label ?typeLabel . 
   ?format rdfs:label ?formatLabel .
}
GROUP BY ?formatLabel ?count
ORDER BY DESC(?count)
</pre>

**FO3.** What is the realtion between the usage of formats and scale of resources?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT DISTINCT ?extentLabel ?formatLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource rdfs:label ?catalogueLabel ; 
             &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://purl.org/dc/terms/extent&gt; ?extent ; 
             &lt;http://data.open.ac.uk/mudow/ontology/scope/format&gt; ?format .
   ?resourceType rdfs:label ?typeLabel . 
   ?extent rdfs:label ?extentLabel . ?format rdfs:label ?formatLabel .
}
GROUP BY ?extentLabel ?formatLabel ?count
ORDER BY ?extentLabel DESC(?count)
</pre>

**FO4.** Which musical features do they mostly represent?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT DISTINCT  ?featureLabel ?formatLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource &lt;http://xmlns.com/foaf/0.1/primaryTopic&gt; ?feature ;
             &lt;http://data.open.ac.uk/mudow/ontology/scope/format&gt; ?format .
   ?feature rdfs:label ?featureLabel . ?format rdfs:label ?formatLabel .
}
GROUP BY ?featureLabel ?formatLabel ?count
ORDER BY  DESC(?count)
</pre>

**FO5.** In which situations/tasks are they used? How many times?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT DISTINCT ?formatLabel ?taskLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource &lt;http://data.open.ac.uk/mudow/ontology/scope/format&gt; ?format ;
             &lt;http://data.open.ac.uk/mudow/ontology/situation/task&gt; ?task .
   ?task rdfs:label ?taskLabel .
   ?format rdfs:label ?formatLabel .
}
GROUP BY ?taskLabel ?formatLabel ?count
ORDER BY ?formatLabel desc(?count)
</pre>

## About symbolic notation

**SN1.** How many resources on the web offer a symbolic representation of music?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT DISTINCT (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource &lt;http://schema.org/featureList&gt; 
                &lt;http://data.open.ac.uk/mudow/ontology/feature/symbolic-machine-readable&gt; .
}
</pre>

**SN2.** How much of the content on the Web representing symbolic notation is machine readable?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT DISTINCT ?extentLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource &lt;http://schema.org/featureList&gt; 
                &lt;http://data.open.ac.uk/mudow/ontology/feature/symbolic-machine-readable&gt; ;
             &lt;http://purl.org/dc/terms/extent&gt; ?extent.
  ?extent rdfs:label ?extentLabel . 
}
GROUP BY ?extentLabel ?count
ORDER BY desc(?count)
</pre>

**SN3.** Which type of resource deals more with symbolic notation?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT DISTINCT ?typeLabel (count(DISTINCT ?resource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://schema.org/featureList&gt; 
                  &lt;http://data.open.ac.uk/mudow/ontology/feature/symbolic-machine-readable&gt; .
  ?resourceType rdfs:label ?typeLabel . 
  ?extent rdfs:label ?extentLabel . 
}
GROUP BY ?typeLabel ?count
ORDER BY desc(?count)
</pre>

**SN4.** Which type of resource deals more with symbolic notation when compared to the total number of those type of resources?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT DISTINCT ?typeLabel (count(DISTINCT ?resource) AS ?symbCount) (count(DISTINCT ?genResource) AS ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?genResource &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType .
   ?resource &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://schema.org/featureList&gt; 
                  &lt;http://data.open.ac.uk/mudow/ontology/feature/symbolic-machine-readable&gt; .
  ?resourceType rdfs:label ?typeLabel .  
}
GROUP BY ?typeLabel ?symbCount ?count
ORDER BY desc(?count)
</pre>

**SN5.** Which symbolic features are more/less represented? 

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT DISTINCT ?featureLabel (count(DISTINCT ?resource) AS ?symbCount)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://xmlns.com/foaf/0.1/primaryTopic&gt; ?feature .
  ?resourceType rdfs:label ?typeLabel . 
  ?feature rdfs:label ?featureLabel
}
GROUP BY ?featureLabel ?symbCount 
ORDER BY desc(?symbCount)
</pre>

**SN6.** Which symbolic features are more/less represented when compared to the scale of those type of resources? 

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT DISTINCT ?featureLabel ?extentLabel (count(DISTINCT ?resource) AS ?symbCount)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource &lt;http://purl.org/spar/datacite/hasGeneralResourceType&gt; ?resourceType ;
             &lt;http://xmlns.com/foaf/0.1/primaryTopic&gt; ?feature ;
             &lt;http://purl.org/dc/terms/extent&gt; ?extent.
  ?resourceType rdfs:label ?typeLabel . 
  ?extent rdfs:label ?extentLabel . 
  ?feature rdfs:label ?featureLabel
}
GROUP BY ?featureLabel ?extentLabel ?symbCount 
ORDER BY desc(?symbCount)
</pre>

**SN7.** How many projects extract features/metadata from audio files rather than scores? 

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT DISTINCT ?typeLabel (count(DISTINCT ?resource) AS ?symbCount)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
  ?resource &lt;http://dbpedia.org/ontology/category&gt; ?resourceType .
  ?resourceType rdfs:label ?typeLabel .
}
GROUP BY ?typeLabel ?symbCount 
ORDER BY ?typeLabel desc(?symbCount)
</pre>

**SN8.** Which one (audio/score) offers more information on features? 

i.e. how many resources dealing with audio offer information on melody, harmony, rhythm… and how many resources focused on scores offer the same information?

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT DISTINCT ?typeLabel ?featureLabel (count(DISTINCT ?resource) AS ?symbCount)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource &lt;http://xmlns.com/foaf/0.1/primaryTopic&gt; ?feature ;
             &lt;http://dbpedia.org/ontology/category&gt; ?resourceType .
  ?resourceType rdfs:label ?typeLabel .
  ?feature rdfs:label ?featureLabel .
}
GROUP BY ?typeLabel ?featureLabel ?symbCount 
ORDER BY ?typeLabel desc(?symbCount) ?featureLabel 
</pre>

**SN9.** When features are represented, what are the main tasks? 

<pre>
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;
SELECT ?taskLabel ?featureLabel (count(DISTINCT ?resource) AS ?symbCount)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?resource &lt;http://xmlns.com/foaf/0.1/primaryTopic&gt; ?feature ;
             &lt;http://data.open.ac.uk/mudow/ontology/situation/task&gt; ?task .
   ?task rdfs:label ?taskLabel .
  ?feature rdfs:label ?featureLabel .
}
GROUP BY ?taskLabel ?featureLabel ?symbCount 
ORDER BY ?taskLabel  ?featureLabel desc(?symbCount) 
</pre>

