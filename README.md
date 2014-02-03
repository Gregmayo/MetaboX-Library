MetaboX v1 beta 7/12/2013

GENERAL USAGE NOTES
-------------------
- The MetaboX library is a PHP framework to build metabolic networks from KEGG.
  It provides classes to retrieve information about compounds, enzymes and reaction,
  cache these to file and build three different networks: reactants, unipartite and bipartite.
  KEGG (http://www.genome.jp/kegg/) is the default resource provider, but it is
  possible to implement alternative interfaces to retrieve data from
  other resource providers.
  Default cache storage is JSON file, but it is possible to create alternative
  cache methods (e.g. write data to a database).

- MetaboX provides several network layouts depending on available resources:
  reactants graph: it is built upon compounds and reactions
  enzymes graph: it is built upon enzymes (unipartite or bipartite)
  
- Released under GNU AFFERO GENERAL PUBLIC LICENSE v3

INSTALLING AND RUNNING
----------------------
- The MetaboX library requires PHP 5.3+ installed. Just put the library wherever
  you prefer on your system. You can find some examples in 'examples' folder.

- The examples can be run as follows:
  + php path/to/examples/build_reactants_graph.php path/to/compounds.csv
  + php path/to/examples/resources_loader.php path/to/compounds.csv

- 'compounds.csv' file must be a csv (comma separated value) file with a list
  of KEGG IDs compounds.

- It is possible to use 'config.ini' file to configure some options about
  resource providers and cache folders.

##############################################################################

Contacts:
+ Francesco Maiorano <frmaiorano@gmail.com>       LabGTP, High Performance Computing and Networking Institute, National Research Council of Italy
+ Luca Ambrosino     <luca.bioinfo@gmail.com>     Dept. of Agricultural Sciences, University of Naples Federico II, Italy
+ Mario Guarracino   <mario.guarracino@gmail.com> LabGTP, High Performance Computing and Networking Institute, National Research Council of Italy

