###Handaling Event

We have created an event for adding annotation to solr doc. For event to trigger we need to add event dispatcher in annotation_store entity save method. 
**This event accepts 2 argument :**
* Reference Id of the resource we need to map the annotation
* Solr server ID i.e machine name of the server created by Search API

Once we add the event dispatcher it will trigger the event to store the annotation in the solr doc.

 