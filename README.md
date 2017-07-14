# MuDOW Linked Data SPARQL queries

The following selection of SPARQL queries is meant to be a useful guide for the user who wants to discover which musical data is available on the web and how to reuse it! 

MuDOW RDF dataset can be queried at [https://data.open.ac.uk/sparql](https://data.open.ac.uk/sparql). Be sure to include the FROM clause [FROM <http://data.open.ac.uk/context/mudow>](http://data.open.ac.uk/context/mudow) to query MuDOW graph. 
Here an example resource for starting browsing: [MIDI Linked Dataset](http://data.open.ac.uk/mudow/2c52e5179258305c74fcc637615eb123). 


## General queries

The following examples show an overview of resources gathered and described in MuDOW survey.

**GQ1.** How many resources have been included in the survey?

<pre>
SELECT (COUNT(?s) as ?count)
FROM &lt;http://data.open.ac.uk/context/mudow&gt;
WHERE {
   ?s <http://xmlns.com/foaf/0.1/homepage> ?o .
}
</pre> 