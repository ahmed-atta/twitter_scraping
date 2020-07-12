from selenium import webdriver
from selenium.webdriver.common.keys import Keys
from selenium.common.exceptions import NoSuchElementException, StaleElementReferenceException
from time import sleep
import json
import datetime


# edit these three variables
user = 'Ask_Al_Qassim'
scroll = 10

# only edit these if you're having problems
delay = 5  # time to wait on each page load before reading the page
driver = webdriver.Firefox()  # options are Chrome() Firefox() Safari()


# don't mess with this stuff
twitter_ids_filename = user + '_ids.json'

user = user.lower()
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
