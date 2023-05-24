#!/Users/hourianalthunayangmail.com/opt/anaconda3/bin/python
# -- coding: utf-8 --

# Importing modules
import warnings
warnings.filterwarnings("ignore")
import pandas as pd
import os
import sys
from sklearn.model_selection import train_test_split 


def split():

 os.chdir('..')
# Read data
 newId = sys.argv[1]
#  newId = '63fe81e7cbb6f440e30ed252'

 fileDir = '/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/'+ newId+'API.csv'
 file = pd.read_csv(fileDir)
# Split data
 
 X=file['comment_text']
 if 'toxic' in file.columns:
  y=file['toxic']

  X_train, X_test, y_train, y_test = train_test_split(X, y,stratify=y,test_size=0.2)

  y_testDF = pd.DataFrame(y_test)
  yCount_test= y_testDF.shape[0]
  y_trainDF = pd.DataFrame(y_train)
  yCount_train = y_trainDF.shape[0]
  X_trainDF = pd.DataFrame(X_train)
  X_testDF = pd.DataFrame(X_test)

  X_testDF = X_testDF.reset_index(drop=True)
  X_trainDF = X_trainDF.reset_index(drop=True)
  y_testDF = y_testDF.reset_index(drop=True)
  y_trainDF = y_trainDF.reset_index(drop=True)

  new_CSV_file='/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/'+newId+'test.csv'
  file_new= open(new_CSV_file,'w')
  new_test_df = pd.read_csv(new_CSV_file,names=['comment_text','toxic'])
  index=0    
  for y in range(yCount_test):
        comment =X_testDF.at[index,'comment_text']
        new_test_df.loc[index,'comment_text'] = comment
        new_test_df.loc[index,'toxic'] = y_testDF.at[index,'toxic']
        index=index+1
  new_test_df.to_csv(new_CSV_file) 
  file_new.close() #save test file 

  new_CSV_file1='/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/'+newId+'train.csv'
  file_new= open(new_CSV_file1,'w')
  new_train_df = pd.read_csv(new_CSV_file1,names=['comment_text','toxic'])
  index=0    
  for index in range(yCount_train):
        comment =X_trainDF.at[index,'comment_text']
        new_train_df.loc[index,'comment_text'] = comment
        new_train_df.loc[index,'toxic'] = y_trainDF.at[index,'toxic']
        index=index+1

  new_train_df.to_csv(new_CSV_file1) 
  file_new.close() #save train file 

if __name__ == '__main__':
    split()
    print('done')
