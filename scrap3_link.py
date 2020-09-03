from selenium import webdriver
from selenium.webdriver.common.keys import Keys
from selenium.common.exceptions import NoSuchElementException, StaleElementReferenceException
from time import sleep
import json
import datetime
import mysql.connector
import requests

# Lookup Data
userCities =	{
	"askjeddh":236,
	"Ask_jeddeh1":236,
	"Ask_JD1":236,
	"Ask_makkah_":232,
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
	"Ask_JD1":5,
	"Ask_makkah_":1,
	"Ask_almadina":3,
	"Ask_Alriyadh1":4,
	"ask_jizan_":6,
	"Ask_Al_Qassim":7,
	"Ask_6aif":8,
	"ask_tourist":9
}

###########################################
def format_day(date):
    day = '0' + str(date.day) if len(str(date.day)) == 1 else str(date.day)
    month = '0' + str(date.month) if len(str(date.month)
                      ) == 1 else str(date.month)
    year = str(date.year)
    return '-'.join([year, month, day])


def form_url(since, until):
    p1 = 'https://twitter.com/search?f=tweets&vertical=default&q=from%3A'
    p2 = screen_name + '%20since%3A' + since + '%20until%3A' + \
        until + 'include%3Aretweets&src=typd'
    return p1 + p2


def increment_day(date, i):
    return date + datetime.timedelta(days=i)


#
# only edit these if you're having problems
delay = 10  # time to wait on each page load before reading the page
driver = webdriver.Firefox()  # options are Chrome() Firefox() Safari()
# don't mess with this stuff


mydb = mysql.connector.connect(
    host="localhost", user="root", passwd="mypassword", database="askmadina")
mycursor = mydb.cursor()
#mycursor.execute("SELECT users.id,users.last_name FROM users where type ='Scrap'")
#myresult = mycursor.fetchall()
url = "http://www.askmadina.com/_API/api_get.php"
r = requests.get(url)
users = json.loads(r.text.encode().decode('utf-8-sig'))

for row in users:
    screen_name = row['screen_name']
    user_id = row['id']
    #start = datetime.datetime(2020,8,7)  # year, month, day
    #end = datetime.datetime(2020,6,10)  # year, month, day
    start = datetime.datetime.now()
    start = start - datetime.timedelta(days=2) 
    end = datetime.datetime.now() 
    days = (end - start).days - 1
    ids = []

    for day in range(days):
	    d1 = format_day(increment_day(start, 0))
	    d2 = format_day(increment_day(start, 1))
	    url = form_url(d1, d2)
	    print(url)
	    print(d1)
	    driver.get(url)
	    sleep(delay)

	    try:
	        scroll = 15
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
	            # found_tweets = driver.find_elements_by_css_selector(tweet_selector)
	            # increment += 5
	            scroll -= 1

	    except NoSuchElementException:
	        print('no tweets on this day')

	    start = increment_day(start, 1)

    tweet_ids = list(set(ids))
    for tweet_id in tweet_ids:
	    sql = "INSERT INTO askboot_crawling_tweets (tweet_id, user_id , screen_name ,city_id,tag_id) VALUES (%s, %s ,%s,%s,%s)"
	    val = (tweet_id,user_id,screen_name,userCities[screen_name], userTags[screen_name])
	    mycursor.execute(sql, val)
	    mydb.commit()






print('all done here')
driver.close()
  
