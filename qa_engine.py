import mysql.connector
mydb = mysql.connector.connect(host="localhost",user="root",passwd="",database="qa_engine")
mycursor = mydb.cursor()
mycursor.execute("SELECT * FROM accounts")
myresult = mycursor.fetchall()
for x in myresult:
  end = str(x[3])
  end = end.replace("-", "")
  with open(x[1]+"_"+end+".json", 'w') as f:
    f.write("Now the file has more content!")
  
