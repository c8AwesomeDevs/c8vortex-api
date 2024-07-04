"""
This script is used to spam random datapoints into the symbols(triangles and pentagons)
for when you are debugging the zone/region classification of the symbols
"""

import requests
import time
from datetime import datetime, date, timedelta
import random

# change the element id; you should also edit the api.php to allow this route
# also in the DB companies table change the max_datapoints to -1
# http://localhost:8000/api/elements/{id-here}/attribute-values--testing
url = 'http://localhost:8000/public/api/elements/3/attribute-values--testing'

# change the date to when you what the loop to start
# NOTE: the loop will count backwards from this date
startdate = datetime.strptime('2024-01-19', "%Y-%m-%d")

# change the range() as to how many datapoints you need
for i in range(3):
    d = startdate - timedelta(days=i)
    obj = {
        'company_id': '1',
        'date': d.strftime("%Y-%m-%d"),
        'time': "00:00:00",
        'acetylene': random.uniform(0,450),
        'ethylene': random.uniform(0,1800),
        'methane': random.uniform(0,400),
        'ethane': random.uniform(0,900),
        'hydrogen': random.uniform(0,1800),
        'carbon_monoxide': random.uniform(0,2000),
        'carbon_dioxide': random.uniform(0,2000),
        'oxygen': random.uniform(0,2000),
    }
    print(obj)

    res = requests.post(url, json = obj)
    if res.status_code == 200:
        print("200 OK")
    if res.status_code != 200:
        print("Error!")
        print(res.text)

    print(i)

print("Done.........................")
# you might need to truncate the attribute_values table everytime you run this script