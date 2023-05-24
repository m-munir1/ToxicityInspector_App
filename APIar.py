#!/Users/hourianalthunayangmail.com/opt/anaconda3/bin/python
# -- coding: utf-8 --

from googleapiclient import discovery
import json
from csv import reader
import pymongo
import pandas
import sys

client = pymongo.MongoClient("mongodb://localhost:27017/")
db = client.Toxicity_Inspector
collection = db.CommentsByAPI 

API_KEY = 'AIzaSyBUuGxz3wnueQy-IKw1NBJ94fHtU28qH4w'

#API code
client = discovery.build(
  "commentanalyzer",
  "v1alpha1",
  developerKey=API_KEY,
  discoveryServiceUrl="https://commentanalyzer.googleapis.com/$discovery/rest?version=v1alpha1",
  static_discovery=False,
)
newId = sys.argv[1]
fileDir = '/Applications/MAMP/htdocs/2_ToxicityInspector_App//Uploads/'+newId+'.csv'

num =0 
num2=0
# for testing the number of comments 
with open(fileDir, 'r' ,  encoding='utf-8') as read_obj:
    csv_reader = reader(read_obj)
    for row in csv_reader:
     num+=1
#skip first line i.e. read header first and then iterate over each row od csv as a list
with open(fileDir, 'r' , encoding='utf-8') as read_obj:
    csv_reader = reader(read_obj)
    header = next(csv_reader)
    # Check file as empty
    if header != None:
       
            
        # Iterate over each row after the header in the csv
        with open('result.json', 'w' , encoding='utf-8') as out_file:
            with open('comment.json', 'w', encoding='utf-8') as out_file2:
                df = pandas.read_csv(fileDir,usecols = ['comment_text'])
         
                out_file.write('[')
                
                for i , row  in df.iterrows(): # row variable is a list that represents a row in csv
             
                    # convering each row into string
                    text = ''.join(row)
                    text = text.replace(",", " ") 
            
                    analyze_request = {
                      'comment': { 'text': text },
                      'requestedAttributes': {'TOXICITY': {}},
                      "languages": ['ar']
                    }
                    result = {}
                    result['fileID'] = {'fileID' :newId}
                    result['comment'] = {
                        'comment' : text
                    }
                    
                    num2+=1
                    response = client.comments().analyze(body=analyze_request).execute()
                    result['response']=response
                    json.dump(result, out_file, indent=2)
                    
                
                    if(num2+1<num):
                     out_file.write(',')
                     
                out_file.write(']')
                

with open("result.json") as f:  
    file_data = json.load(f)

collection.insert_many(file_data)