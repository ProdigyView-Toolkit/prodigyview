// The PHP tests connect as this application user against the prodigyview
// database. The root user remains only for Mongo's container initialization.
db = db.getSiblingDB('prodigyview');
db.createUser({
  user: 'prodigyview',
  pwd: 'prodigyview',
  roles: [
    {
      role: 'readWrite',
      db: 'prodigyview'
    }
  ]
});
