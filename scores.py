#!/Users/hourianalthunayangmail.com/opt/anaconda3/bin/python
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
import string

newId = sys.argv[1]
# newId= '63dbbd733794116f92053be4'
# read training and testing data after splitting
train ='/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/'+newId+'train.csv'
train = pd.read_csv(train)
test ='/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/'+newId+'test.csv'
test = pd.read_csv(test)

# convert the test and train data into dataframe and reset indexes 
train= pd.DataFrame(train)
train.reset_index(drop=True)
test= pd.DataFrame(test)
test.reset_index(drop=True)

train['comment_text'] = train['comment_text'].str.lower()
test['comment_text'] = test['comment_text'].str.lower()

punctuations_list = string.punctuation
def remove_punctuations(text):
    temp = str.maketrans('', '', punctuations_list)
    return text.translate(temp)

train['comment_text'] = train['comment_text'].apply(lambda x: remove_punctuations(x))
test['comment_text'] = test['comment_text'].apply(lambda x: remove_punctuations(x))

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

# plt.barh(words[:5], coefs[:5]) # toxic
# plt.barh(words_nontoxic[:5], coefs_nontoxic[:5])
# plt.title("Toxicity")
# path = '/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/'+newId+'Words.png'
# plt.savefig(path, bbox_inches='tight', dpi=300)
# save the produced plot to image to display it in the website 

# store our score in a variables 
f1__score = report['macro avg']['f1-score'] 
# print the score
print("{:.2f}".format(f1__score))
print(words[:5])
print(',',words_nontoxic[:5])
