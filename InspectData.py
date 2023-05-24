#!/Users/hourianalthunayangmail.com/opt/anaconda3/bin/python
# -- coding: utf-8 --

import warnings
warnings.filterwarnings("ignore")
import pandas as pd
import os
import re
import gensim
from gensim.utils import simple_preprocess
import nltk
nltk.download('stopwords')
from nltk.corpus import stopwords
import gensim.corpora as corpora
import pyLDAvis
import pyLDAvis.gensim_models
import pyLDAvis.gensim_models as gensimvis
import pickle 
import sys

def main():
 os.chdir('..')
 #Read data
 newId = sys.argv[1]
#  newId= '63dbb48c3794116f92053be3'
 fileDir = '/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/'+newId+'.csv'
 training = pd.read_csv(fileDir)
 #Load the regular expression library
 #Remove punctuation
 training['comment_text_processed'] = \
 training['comment_text'].map(lambda x: re.sub('[,\:n.!?"]', '', x))
 training['comment_text_processed'] = \
 training['comment_text_processed'].map(lambda x: x.lower()) # to lower case

 #Remove stop words
 stop_words = stopwords.words('english')
 stop_words.extend(['from', 'subject', 're', 'edu', 'use'])

 def sent_to_words(sentences):
    for sentence in sentences:
        yield(gensim.utils.simple_preprocess(str(sentence), deacc=True))

 def remove_stopwords(texts):
    return [[word for word in simple_preprocess(str(doc)) 
             if word not in stop_words] for doc in texts]

 data = training.comment_text_processed.values.tolist()
 data_words = list(sent_to_words(data))
 data_words = remove_stopwords(data_words)

 id2word = corpora.Dictionary(data_words) #Create Dictionary

 texts = data_words # Create Corpus

 corpus = [id2word.doc2bow(text) for text in texts] #Term Document Frequency

 num_topics = 10 #Number of topics
 #Build LDA model
 lda_model = gensim.models.LdaMulticore(corpus=corpus,
                                        id2word=id2word,
                                        num_topics=num_topics)
 
 #Visualize the topics
 LDAvis_data_filepath = os.path.join('/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/'+newId)
 if 1 == 1:
     LDAvis_prepared = gensimvis.prepare(lda_model, corpus, id2word)
     with open(LDAvis_data_filepath, 'wb') as f:
       pickle.dump(LDAvis_prepared, f)
 #Load the pre-prepared pyLDAvis data from disk
 with open(LDAvis_data_filepath, 'rb') as f:
    LDAvis_prepared = pickle.load(f)
 pyLDAvis.save_html(LDAvis_prepared,'/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/'+newId+'.html') 

if __name__ == '__main__':
    main()

