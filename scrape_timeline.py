from selenium import webdriver
from selenium.webdriver.common.keys import Keys
from selenium.common.exceptions import NoSuchElementException, StaleElementReferenceException
from time import sleep
import json
import datetime
import mysql.connector

mydb = mysql.connector.connect(host="localhost", user="root", passwd="mypassword", database="askmadina")
mycursor = mydb.cursor()
# edit these three variables
screen = 'askjeddh'
userIds =	{
	"askjeddh":6,
	"Ask_jeddeh1":4,
	"Ask_Makkah_":5,
	"Ask_almadina":7,
	"Ask_Alriyadh1":8,
	"ask_jizan_":142649,
	"Ask_Al_Qassim":142673,
	"Ask_6aif":142674,
	"ask_tourist":179861
} 
# Lookup Data
userCities =	{
	"askjeddh":236,
	"Ask_jeddeh1":236,
	"Ask_Makkah_":232,
	"Ask_almadina":226,
	"Ask_Alriyadh1":224,
	"ask_jizan_":229,
	"Ask_Al_Qassim":225,
	"Ask_6aif":237,
	"ask_tourist":1
}

userTags =	{
	"askjeddh":5,
	"Ask_jeddeh1":5,
	"Ask_Makkah_":1,
	"Ask_almadina":3,
	"Ask_Alriyadh1":4,
	"ask_jizan_":6,
	"Ask_Al_Qassim":7,
	"Ask_6aif":8,
	"ask_tourist":9
}
scroll = 10

# only edit these if you're having problems
delay = 5  # time to wait on each page load before reading the page
driver = webdriver.Firefox()  # options are Chrome() Firefox() Safari()

user = screen.lower()
# don't mess with this stuff
ids = []



url = "https://twitter.com/" + user
print(url)
driver.get(url)
sleep(delay)

try:
	
	while scroll >= 1:
	    found_tweets = driver.find_elements_by_tag_name('a')
	    print('{} tweets found'.format(len(found_tweets)))

	    for tweet in found_tweets:
	        try:
	            status = tweet.get_attribute('href').split('/')[-2]
	            if status == 'status':
	                id = tweet.get_attribute('href').split('/')[-1]
	                ids.append(id)

	        except StaleElementReferenceException as e:
	        	print('lost element reference', tweet)

	    print('{} total'.format(len(ids)))
	    print('scrolling down to load more tweets')
	    driver.execute_script('window.scrollTo(0, document.body.scrollHeight);')
	    sleep(delay)
	    scroll -= 1

except NoSuchElementException:
	print('no tweets on this day')

tweet_ids = list(set(ids))
for tweet_id in tweet_ids:
	sql = "INSERT INTO askboot_crawling_tweets (tweet_id, user_id , screen_name ,city_id,tag_id) VALUES (%s, %s ,%s,%s,%s)"
	val = (tweet_id,userIds[screen],screen,userCities[screen], userTags[screen])
	mycursor.execute(sql, val)
	mydb.commit()


print('all done here')
driver.close()
