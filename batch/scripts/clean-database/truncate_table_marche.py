from object import engine

with engine.connect() as con:
    result = con.execute("truncate table marche")
    result = con.execute("truncate table acheteur")
    result = con.execute("truncate table titulaire")
    result = con.execute("truncate table marche_titulaires")
    result = con.execute("truncate table lieu")
