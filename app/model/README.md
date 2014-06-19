This is the model LAYER. In here there should probably be three factory classes:

BusinesObjectFactory - this knows how to build a business object.
DataMapperFactory - this knows how to build a datamapper. DataMappers populate/save business objects by communicating with the database
ServiceFactory - to build services. Services facilitate communication between business objects and datamappers.


Since the model is a LAYER in this application there is no one model class, instead we pass the same instance of the ServiceFactory to views
and controllers. The services contain the DataMapperFactory and BusinessObjectFactory.
