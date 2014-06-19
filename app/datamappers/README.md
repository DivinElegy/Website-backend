This is where datamappers go, they need to extend the datamapper abstract class.

The only thing a datamapper needs to know is how to save objects to the database. So SQL should go in a datamapper.

I want to use PDO, so a datamapper will need a PDO instance.
