#psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" <<EOF
#create extension fuzzystrmatch;
#select * FROM pg_extension;
#EOF
set -e

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
create extension fuzzystrmatch;
select * FROM pg_extension;
EOSQL