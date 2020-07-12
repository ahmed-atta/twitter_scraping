from selenium import webdriver
from selenium.webdriver.common.keys import Keys
from selenium.common.exceptions import NoSuchElementException, StaleElementReferenceException
from time import sleep
import json
import datetime
import mysql.connector

# Lookup Data
userCities =	{
	"Ask_jeddeh1":2231711,
	"Ask_Makkah_":2232036,
	"Ask_almadina":2232151,
	"Ask_Alriyadh1":2232671,
	"ask_jizan_":2231735,
	"Ask_Al_Qassim":2233421,
	"Ask_6aif":2233101,
	"ask_tourist":1
}

userTags =	{
	"Ask_jeddeh1":5,
	"Ask_Makkah_":1,
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
    p2 = user + '%20since%3A' + since + '%20until%3A' + \
        until + 'include%3Aretweets&src=typd'
    return p1 + p2


def increment_day(date, i):
    return date + datetime.timedelta(days=i)


#
# only edit these if you're having problems
delay = 5  # time to wait on each page load before reading the page
driver = webdriver.Firefox()  # options are Chrome() Firefox() Safari()
# don't mess with this stuff


mydb = mysql.connector.connect(
    host="localhost", user="root", passwd="mypassword", database="askmadina")
mycursor = mydb.cursor()
mycursor.execute("SELECT users.id,users.last_name FROM users where type ='Scrap'")
myresult = mycursor.fetchall()
for row in myresult:
    user = row[1]
    screen_name = row[1]
    user_id = row[0]
    start = datetime.datetime(2020,7,10)  # year, month, day
    #end = datetime.datetime(2020,6,10)  # year, month, day
    end = datetime.datetime.now() 
    # twitter_ids_filename = '/var/www/html/askboot_py/twitter_scraping-master/all_ids.json'
    twitter_ids_filename = end.strftime("%Y%m%d") +"_" + user +".json"
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
	        scroll = 12
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

    try:
	    with open(twitter_ids_filename) as f:
	        all_ids = ids + json.load(f)
	        data_to_write = list(set(all_ids))
	        print('tweets found on this scrape: ', len(ids))
	        print('total tweet count: ', len(data_to_write))
    except FileNotFoundError:
	    with open(twitter_ids_filename, 'w') as f:
	        all_ids = ids
	        data_to_write = list(set(all_ids))
	        print('tweets found on this scrape: ', len(ids))
	        print('total tweet count: ', len(data_to_write))

    with open(twitter_ids_filename, 'w') as outfile:
	    json.dump(data_to_write, outfile ,indent=2)





print('all done here')
driver.close()
  
