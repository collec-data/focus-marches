from object import engine
with engine.connect() as con:
    result = con.execute("truncate table sirene")
