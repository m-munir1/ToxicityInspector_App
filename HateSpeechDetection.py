#!/Users/hourianalthunayangmail.com/opt/anaconda3/envs/spyder/bin/python
# -- coding: utf-8 --

import numpy as np
import pandas as pd
import matplotlib.pyplot as plt
import seaborn as sb 
from sklearn.feature_extraction.text import TfidfVectorizer
import nltk
import string
import warnings
from nltk.corpus import stopwords
from nltk.stem import WordNetLemmatizer
from tensorflow import keras
from keras import layers
import tensorflow as tf
from keras.utils import to_categorical
tf.compat.v1.disable_v2_behavior()
tf.compat.v1.disable_eager_execution()
from tensorflow.python.ops.numpy_ops import np_config
np_config.enable_numpy_behavior()
tf.compat.v1.global_variables_initializer()

import shap
from tensorflow.keras.preprocessing.text import Tokenizer
from tensorflow.keras.preprocessing.sequence import pad_sequences
from keras.callbacks import EarlyStopping, ReduceLROnPlateau
print(tf.__version__)
print(shap.__version__)
print(keras.__version__)
nltk.download('stopwords')
nltk.download('omw-1.4')
nltk.download('wordnet')
warnings.filterwarnings('ignore')

newId= '63dbb48c3794116f92053be3'
# file ='/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/'+newId+'.csv'
testFile = '/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/'+newId+'test.csv'
trainFile = '/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/'+newId+'train.csv'
# file = pd.read_csv(file)
testFile = pd.read_csv(testFile)
trainFile = pd.read_csv(trainFile)

testFile['comment_text'] = testFile['comment_text'].str.lower()
trainFile['comment_text'] = trainFile['comment_text'].str.lower()

punctuations_list = string.punctuation
def remove_punctuations(text):
    temp = str.maketrans('', '', punctuations_list)
    return text.translate(temp)

trainFile['comment_text']= trainFile['comment_text'].apply(lambda x: remove_punctuations(x))
testFile['comment_text']= testFile['comment_text'].apply(lambda x: remove_punctuations(x))
print('after_remove_punctuation')

def remove_stopwords(text):
    stop_words = stopwords.words('english')
 
    imp_words = []

    # Storing the important words
    for word in str(text).split():
 
        if word not in stop_words:
            # Let's Lemmatize the word as well, before appending to the imp_words list
           
            lemmatizer = WordNetLemmatizer()
            lemmatizer.lemmatize(word)
            imp_words.append(word)
 
    output = " ".join(imp_words)

    return output

testFile['comment_text'] = testFile['comment_text'].apply(lambda text: remove_stopwords(text))
trainFile['comment_text'] = trainFile['comment_text'].apply(lambda text: remove_stopwords(text))

print('after_remove_stopwords')


X_train = trainFile['comment_text']
X_val = testFile['comment_text']
Y_train = trainFile['toxic']
Y_val = testFile['toxic']

# X_train, X_val, Y_train, Y_val = train_test_split(features,target,test_size=0.2,random_state=22)
# print('shapes after splitting')
# print(X_train.shape)
# print(X_val.shape)

Y_train = pd.get_dummies(Y_train)
Y_val = pd.get_dummies(Y_val)

# print('shapes after get_dummies')
# print(Y_train.shape)
# print(Y_val.shape)

X_train=pd.DataFrame(X_train)
comment_text = X_train['comment_text']
raw_data = comment_text.values
X_val=pd.DataFrame(X_val)
comment_text2 = X_val['comment_text']
raw_data2 = comment_text2.values

tfidf_vc = TfidfVectorizer(analyzer = "word", ngram_range = (1, 2))
train_vc = tfidf_vc.fit_transform(comment_text)
val_vc = tfidf_vc.transform(comment_text2)

trainVc = train_vc
valVc = val_vc
# print('train_vc',type(train_vc))
tr = pd.DataFrame(trainVc.toarray()) #dataframe
te = pd.DataFrame(valVc.toarray()) #dataframe

print('train_vc',type(train_vc))
print('val_vc',type(val_vc))

max_words = 10000
max_len = tr.shape[1]

token = Tokenizer(num_words=max_words,lower=True, split=' ')
token.fit_on_texts(X_train)


#Generating token embeddings
Training_seq = token.texts_to_sequences(X_train)
Training_pad = pad_sequences(Training_seq,maxlen=50, padding='post',truncating='post')
 
Testing_seq = token.texts_to_sequences(X_val)
Testing_pad = pad_sequences(Testing_seq,maxlen=50,padding='post',truncating='post')

model = keras.models.Sequential([
    layers.InputLayer(input_shape=(max_len,)),
    layers.Embedding(max_words, 32, input_length=max_len,trainable=True),
    layers.Bidirectional(layers.LSTM(16)),
    layers.Dense(512, activation='relu', kernel_regularizer='l1'),
    layers.BatchNormalization(),
    layers.Dropout(0.3),
    layers.Dense(2, activation='softmax')
])
 # categorical_crossentropy
model.compile(loss='categorical_crossentropy',optimizer='adam', metrics=['accuracy'])
print('model.summary') 
model.summary()

keras.utils.plot_model(model,show_shapes=True,show_dtype=True,show_layer_activations=True)


es = EarlyStopping(patience=3, monitor = 'val_accuracy',restore_best_weights = True)
 
lr = ReduceLROnPlateau(patience = 2, monitor = 'val_loss', factor = 0.5, verbose = 0)

# print('raw_data',raw_data)
#history= model.fit(tr,Y_train, validation_split=0.1,epochs=5,.......)

history = model.fit(tr, Y_train, validation_split=0.1, epochs=2, verbose=1, batch_size=32, callbacks=[lr, es])

# history = model.fit(tf.expand_dims(raw_data, axis=-1), Y_train,validation_data=(X_val, Y_val),epochs=50,verbose=1,batch_size=32,callbacks=[lr, es])

y_binary = to_categorical(Y_val)
Results= model.evaluate(te, y_binary, batch_size=5)
# print("test loss, test acc:", Results)
history_df = pd.DataFrame(history.history)
# print(history_df.info)
history_df.loc[:, ['loss', 'val_loss']].plot()
history_df.loc[:, ['acc', 'val_acc']].plot()
plt.show()

print("test loss, test acc:", Results)
# compute SHAP values
# background = X_train[np.random.choice(X_train.shape[0], 100, replace=False)]
# background = X_train[np.random.choice(X_train.shape[0],100, replace=False)] , 

shap.initjs()
# data_for_prediction = comment_text.values.astype('float')
# background = comment_text2.values.astype('float')
# print('tr',tr)
# print('te',te)
# t_exp = tr.to_numpy()
# te_exp = te.to_numpy()
# t_exp.astype('object')
# te_exp.astype('object')

# te_exp = np.asarray(te)

explainer = shap.KernelExplainer(model,tr.sample(0))

shap_values = explainer.shap_values(te)

fig = plt.figure()
shap.plots.bar(shap_values[0],te,plot_type='bar' ,max_display=10, show = False)
plt.gcf().set_size_inches(20,6)
plt.show()


