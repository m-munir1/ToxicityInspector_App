#!/Users/hourianalthunayangmail.com/opt/anaconda3/envs/spyder/bin/python
# -- coding: utf-8 --


import warnings
warnings.filterwarnings("ignore")
import pandas as pd
import os
from lime.lime_text import LimeTextExplainer
from sklearn.pipeline import make_pipeline
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.svm import SVC
from sklearn.metrics import f1_score
import sys

sys.path
def main():
 os.chdir('..')

 #Read data
 
 newId = sys.argv[1]
 index = sys.argv[2]
 comment = sys.argv[3:]
#  newId = id.replace(' ', '')
 newComment = ''
 for i in range (len(comment)):
    newComment = newComment+' '+comment[i]
 print(newComment)
 trainFileDir ='/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/'+newId+'train.csv'
 train_df = pd.read_csv(trainFileDir)
 testFileDir ='/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/'+newId+'test.csv'
 test_df = pd.read_csv(testFileDir)

 test_df=pd.DataFrame(test_df)
 train_df=pd.DataFrame(train_df)
 test_df.reset_index(drop=True)
 train_df.reset_index(drop=True)
## vectorize to tf-idf vectors
 tfidf_vc = TfidfVectorizer(analyzer = "word", ngram_range = (1, 2), stop_words = 'english', lowercase = True)
 train_vc = tfidf_vc.fit_transform(train_df["comment_text"])
 val_vc = tfidf_vc.transform(test_df["comment_text"])
 model = SVC(kernel='linear' ,class_weight='balanced',probability=True)
 model = model.fit(train_vc, train_df.toxic)
 
 val_pred = model.predict(val_vc)

 val_cv = f1_score(test_df.toxic, val_pred, average = "binary")

 c = make_pipeline(tfidf_vc, model)
 class_names = ["Toxic", "non-Toxic"]
 explainer = LimeTextExplainer(class_names = class_names)
 exp = explainer.explain_instance(newComment, c.predict_proba, num_features = 10)

 exp.as_list()
 exp_filepath = os.path.join('/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/'+newId+index)
#  exp.show_in_notebook(text=test_df["comment_text"][idx], labels=(1,))
 exp.save_to_file('/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/'+newId+index+'.html')

 
if __name__ == '__main__':
    main()