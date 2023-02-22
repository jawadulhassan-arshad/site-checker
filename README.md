# site-checker
This is a simple script which can be used to see if a website is online or offline. I used it on a server and it helps us to make sure all of our websites are working fine and online. simply execute this script via cron job and insert your email address for alerts and insert website names in the text file check.txt for listing websites.

To make this script work you need to place a check.txt file in the same location where the php script is hosted. You can add websites in following combination
websiteurl|keyword

The script will visit the website and search for the keyword in the content. If it's found then the script will believe that website is online. It also looks for http status code and 200 means the website is running fine.

How to create a check.txt file ?

https://abc.com|abc
http://websysdynamics.com|web development
https://jawadulhassan.com|web developer & programmer
