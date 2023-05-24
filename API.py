#!/Users/hourianalthunayangmail.com/opt/anaconda3/bin/python
# -- coding: utf-8 --

from unittest import result
from googleapiclient import discovery
from csv import reader
import json
import pymongo
import pandas
import sys

client = pymongo.MongoClient("mongodb://localhost:27017/")
db = client.Toxicity_Inspector
collection = db.CommentsByAPI 

API_KEY = 'AIzaSyBUuGxz3wnueQy-IKw1NBJ94fHtU28qH4w'

LC_CTYPE="en_US.UTF-8"
#API code
client = discovery.build(
  "commentanalyzer",
  "v1alpha1",
  developerKey=API_KEY,
  discoveryServiceUrl="https://commentanalyzer.googleapis.com/$discovery/rest?version=v1alpha1",
  static_discovery=False,
)

newId = sys.argv[1]
# newId= '63dbbd733794116f92053be4'
fileDir = '/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/'+newId+'.csv'

countAll = 0 # to count all comments 
current = 0 # to count the currnet comment 

# for testing the number of comments 
with open(fileDir, 'r') as read_obj:
    csv_reader = reader(read_obj)
    for row in csv_reader:
      countAll+=1 # to count all comments 

#skip first line i.e. read header first and then iterate over each row od csv as a list
with open(fileDir, 'r') as read_obj:
    csv_reader = reader(read_obj)
    header = next(csv_reader)
    # Check file as empty
    if header != None:
            
        # Iterate over each row after the header in the csv
        with open("result.json", "w" , encoding="utf-8") as out_file:
            df = pandas.read_csv(fileDir, usecols = ["comment_text"]) # to read specific column 

            out_file.write('[') # to match json format
            for i , row  in df.iterrows(): # row variable is a list that represents a row in csv
             
            # convering each row into string
                text = ' '.join(row)
                text = text.replace(",", " ") 
            
                analyze_request = {
                  'comment': { 'text': text },
                  'requestedAttributes': {'TOXICITY': {}, 'SEVERE_TOXICITY': {}, 'IDENTITY_ATTACK': {}, 'INSULT': {}, 'PROFANITY': {}, 'THREAT': {}},
                  "languages": ['en']
                }
                result = {}

                result['fileID'] = {'fileID' : newId}

                result['comment'] = {'comment' : text}

                current+=1 # to count the currnet comment 
                response = client.comments().analyze(body=analyze_request).execute()
                result['response'] = response
                json.dump(result, out_file, indent=2)
                
                if(current+1<countAll):
                 out_file.write(',') # to match json format
            out_file.write(']') # to match json format

  
with open("result.json") as f:  # load the data from json to insert it in CommentsByAPI  collection in out database
    file_data = json.load(f)

collection.insert_many(file_data) # insert the json file into CommentsByAPI  collection in out database


