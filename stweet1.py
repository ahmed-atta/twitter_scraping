from selenium import webdriver
from selenium.webdriver.common.keys import Keys
from selenium.common.exceptions import NoSuchElementException, StaleElementReferenceException
from requests.exceptions import ConnectionError
from time import sleep
import json
import datetime
import requests
import mysql.connector


# only edit these if you're having problems
delay = 5  # time to wait on each page load before reading the page
driver = webdriver.Firefox()  # options are Chrome() Firefox() Safari()


#######################
mydb = mysql.connector.connect(host="localhost", user="root", passwd="mypassword", database="askmadina")
mycursor = mydb.cursor()
mycursor.execute("SELECT * FROM askboot_crawling_tweets where status = 1 LIMIT 1000")
myresult = mycursor.fetchall()
for row in myresult:
	tweet_id = row[1]
	#tweet_id = "1270147831538626560"
	user_id = row[2]
	user = row[3]
	city_id = row[4]
	tag_id = row[5]

	replies = []
	temp_list = []
	tweet = ''


	url = "https://twitter.com/" + user + "/status/"+ tweet_id
	print(url)
	driver.get(url)
	sleep(8)

	try:
		scroll = 1
		i = 0 
		temp_articles_count = 0
		deleted = 0
		while scroll == 1:
			articles = driver.find_elements_by_tag_name('article')
			if len(articles) == 0 :
				print('HTML Error')
				url = "https://twitter.com/" + user + "/status/"+ tweet_id
				driver.get(url)
				sleep(16)
			else:
				if temp_articles_count ==  len(articles):
					break
				else :
					temp_articles_count =  len(articles)
				for article in articles:
					try:
						if i == 0 :
							tweet = article.get_attribute('outerHTML')
						else :
							replies.append(article.get_attribute('outerHTML'))
						i += 1
					except StaleElementReferenceException as e:
						print('lost element reference', tweet)
				if len(replies) == 0:
					print('Have NOT Replies')
					sql = "DELETE FROM askboot_crawling_tweets  WHERE tweet_id = %s"
					val = (tweet_id,)
					mycursor.execute(sql, val)
					mydb.commit()
					deleted = 1
					break
				driver.execute_script('window.scrollTo(0, document.body.scrollHeight);')
				sleep(delay)
				print('{} replies found'.format(len(replies)))
		## remove duplication
		temp_list = list(set(replies))

		if deleted == 1 :
			continue
		#url = "http://127.0.0.1/_boot/cron_getTweet.php"
		url = "http://www.askmadina.com/_API/api_post.php"
		payload  = {'tweet_id':tweet_id , 'user':user , 'user_id':user_id,'city_id':city_id,'tag_id':tag_id, 'tweet': tweet , 'replies':  '<hr/>'.join(temp_list)}
		try:
			r = requests.post(url, data = payload)
			print(r.text)
			#quit() 
			decoded_d = r.content.decode('utf-8-sig')
			data = json.loads(decoded_d)
		except ConnectionError:
			sql = "UPDATE askboot_crawling_tweets SET status = %s WHERE tweet_id = %s"
			val = ('-1', tweet_id)
			mycursor.execute(sql, val)
			mydb.commit()
			continue

		if(data["status"] == 1):
			sql = "DELETE FROM askboot_crawling_tweets  WHERE tweet_id = %s"
			val = (tweet_id,)
			mycursor.execute(sql, val)
			mydb.commit()
		if(data["status"] == 0):
			print(data["error"])
			#sql = "UPDATE askboot_crawling_tweets SET status = %s WHERE tweet_id = %s"
			#val = ('-1', tweet_id)
			sql = "DELETE FROM askboot_crawling_tweets  WHERE tweet_id = %s"
			val = (tweet_id,)
			mycursor.execute(sql, val)
			mydb.commit()

	except NoSuchElementException:
		print('HTML Error')


	#try:
	#    with open(twitter_ids_filename) as f:
	#       all_ids = ids + json.load(f)
	#       data_to_write = list(set(all_ids))
	#       print('tweets found on this scrape: ', len(ids))
	#       print('total tweet count: ', len(data_to_write))
	#except FileNotFoundError:
	#    with open(twitter_ids_filename, 'w') as f:
	#        all_ids = ids
	#        data_to_write = list(set(all_ids))
	#        print('tweets found on this scrape: ', len(ids))
	#        print('total tweet count: ', len(data_to_write))
	#
	#with open(twitter_ids_filename, 'w') as outfile:
	#    json.dump(data_to_write, outfile ,indent=2)

driver.close()
