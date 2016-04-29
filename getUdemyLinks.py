from bs4 import BeautifulSoup
from urllib2 import urlopen

class Sprinter():

    _BASE_URL = "https://www.udemy.com/courses/development/"

    def __init__(self):
        self.get_cats()

    def get_cats(self):
         html = urlopen(self._BASE_URL).read()
         soup = BeautifulSoup(html, "lxml")
         cat_list = soup.find_all("span", class_="maincat-title")
         for cat in cat_list:
             ca = cat.string.replace('&', 'and')
             ca = ca.replace(' ', '-')
             ca = ca[1:-1].encode('ascii','ignore')
             css = "#submenu-" + ca +" .sub-list li a"
             links = soup.select(css)
             for l in links:
                 addr = l['href']
                 print(addr)
                 with open('category_list.txt', "a") as f:
                    f.write(addr + '\n')

s = Sprinter()
