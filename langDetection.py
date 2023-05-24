#!/Users/hourianalthunayangmail.com/opt/anaconda3/bin/python
# -- coding: utf-8 --
from langdetect import detect , DetectorFactory
import pandas
import sys

newId = sys.argv[1]
fileDir = '/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/'+ newId+'.csv'


DetectorFactory.seed = 0
df = pandas.read_csv(fileDir) # to read specific column 
df = df['comment_text']
toDetect = df.iloc[0]
# toDetect = toDetect['comment_text']
res = detect(toDetect)
if(res != 'en'):
 toDetect = toDetect[::-1]
 res= detect(toDetect)
print(res)