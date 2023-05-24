#!/Users/hourianalthunayangmail.com/opt/anaconda3/envs/spyder/bin/python
# -- coding: utf-8 --

import sys
import pandas as pd
from sklearn.metrics import classification_report
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.svm import SVC
from matplotlib import pyplot as plt
from sklearn.feature_extraction.text import CountVectorizer
import numpy as np
from nltk.corpus import stopwords
from nltk.stem import WordNetLemmatizer
import pymongo

client = pymongo.MongoClient("mongodb://localhost:27017/")
db = client.Toxicity_Inspector
feedback = db.feedback 
files = db.files 

newId = sys.argv[1]
# newId = '63be56b9c8208377190720d2'

mydoc = feedback.find()
fileName = ''
fileID = ''
feedbackFileName = ''

for doc in mydoc:
    id = str(doc['_id'])
    if(id == newId):
        fileName = doc['FileName']
        feedbackFileName = doc['feedbackName']

mydoc = files.find()
for doc in mydoc:
    name = str(doc['FileName'])
    if( name == fileName):
        fileID = str(doc['_id'])

# read the new training and same testing data
train ='/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/'+newId+'feedback.csv'
train = pd.read_csv(train)
test ='/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/'+fileID+'test.csv'
test = pd.read_csv(test)

# convert the test and train data into dataframe and reset indexes 
train= pd.DataFrame(train)
train.reset_index(drop=True)
test= pd.DataFrame(test)
test.reset_index(drop=True)

def remove_stopwords(text):
    stop_words = stopwords.words('english')
 
    imp_words = []

    # Storing the important words
    for word in str(text).split():
 
        if word not in stop_words:
 
            # Let's Lemmatize the word as well
            # before appending to the imp_words list.
            lemmatizer = WordNetLemmatizer()
            lemmatizer.lemmatize(word)
 
            imp_words.append(word)
 
    output = " ".join(imp_words)
 
    return output

train['comment_text'] = train['comment_text'].apply(lambda text: remove_stopwords(text))
test['comment_text'] = test['comment_text'].apply(lambda text: remove_stopwords(text))

tfidf_vc = TfidfVectorizer(analyzer = "word", ngram_range = (1, 2), stop_words = 'english', lowercase = True)
train_vc = tfidf_vc.fit_transform(train['comment_text'])
val_vc = tfidf_vc.transform(test['comment_text'])

# create a linear svm model
model = SVC(kernel='linear' ,class_weight='balanced',probability=True)
model = model.fit(train_vc, train.toxic)

val_pred = model.predict(val_vc)
report = classification_report(test.toxic, val_pred , output_dict=True)

comment_text = train.comment_text
raw_data = comment_text.values.tolist()

vectorizer = CountVectorizer()
X = vectorizer.fit_transform(raw_data)

comment_text = train.comment_text
toxicity_labels = train.toxic
raw_data = comment_text.values.tolist()
y = np.array(toxicity_labels.values)

vectorizer = CountVectorizer()
X = vectorizer.fit_transform(raw_data)

svm = SVC(kernel='linear',class_weight='balanced',probability=True)
svm.fit(X, y)
coefs = svm.coef_.toarray().flatten()
words = vectorizer.get_feature_names_out()
coefs, words = zip(*sorted(zip(coefs, words), key=lambda x: x[0], reverse=True)[:200000])
coefs_nontoxic, words_nontoxic = zip(*sorted(zip(coefs, words), key=lambda x: x[0], reverse=False)[:200000])


# Binary clssification , make 1 related to what (Toxic) 0 (non-Toxic)
# Coef is vector of wieghts (-1,1)

plt.barh(words[:10], coefs[:10])
plt.barh(words_nontoxic[:10], coefs_nontoxic[:10])
plt.title("Toxicity")
path = '/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/'+newId+'Words.png'
plt.savefig(path, bbox_inches='tight', dpi=300)
# save the produced plot to image to display it in the website 

# store our scores in a variables 
precision = report['macro avg']['precision'] 
recall = report['macro avg']['recall'] 
f1__score = report['macro avg']['f1-score'] 
# print the scores 
print('Precision',precision)
print('Recall',recall)
print('F1__score',f1__score)
