
# MuDOW: SPARQL queries

The following selection of SPARQL queries is meant to be a useful guide to the user who wants to discover which musical data is available on the web and how to reuse it! 

MuDOW RDF dataset can be queried at [https://data.open.ac.uk/sparql](https://data.open.ac.uk/sparql). 

Be sure to include the FROM clause [FROM <http://data.open.ac.uk/context/mudow>](http://data.open.ac.uk/context/mudow) to query MuDOW graph. 
Here an example resource for starting browsing the dataset: [MIDI Linked Dataset](http://data.open.ac.uk/mudow/2c52e5179258305c74fcc637615eb123). 


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
