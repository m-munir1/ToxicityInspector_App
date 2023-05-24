#!/Users/hourianalthunayangmail.com/opt/anaconda3/envs/spyder/bin/python
# -- coding: utf-8 --

#-------- new

#-------- new end
import sys
import pandas as pd
import matplotlib.pyplot as plt
import nltk
import numpy as np
import string
import warnings
from nltk.corpus import stopwords
from nltk.stem import WordNetLemmatizer
from tensorflow import keras
from keras import layers
import shap
from tensorflow.keras.preprocessing.text import Tokenizer
from tensorflow.keras.preprocessing.sequence import pad_sequences
from keras.callbacks import EarlyStopping, ReduceLROnPlateau
from tensorflow.keras import backend as K
nltk.download('stopwords')
nltk.download('omw-1.4')
nltk.download('wordnet')
warnings.filterwarnings('ignore')

newId = sys.argv[1]
# newId= '63dbb48c3794116f92053be3'
testFile = '/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/'+newId+'test.csv'
trainFile = '/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/'+newId+'train.csv'
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

X_train = trainFile['comment_text']
X_val = testFile['comment_text']
Y_train = trainFile['toxic']
Y_val = testFile['toxic']

max_words = 10000
max_len = 10

token = Tokenizer(num_words=max_words,lower=True, split=' ')
token.fit_on_texts(X_train)

#Generating token embeddings
Training_seq = token.texts_to_sequences(X_train)
Training_pad = pad_sequences(Training_seq,maxlen=max_len)

Testing_seq = token.texts_to_sequences(X_val)
Testing_pad = pad_sequences(Testing_seq,maxlen=max_len)


model = keras.models.Sequential([
    layers.InputLayer(input_shape=(max_len,)),
    layers.Embedding(max_words, 32, input_length=max_len,trainable=True),
    layers.Bidirectional(layers.LSTM(16)),
    layers.Dense(512, activation='relu', kernel_regularizer='l1'),
    layers.BatchNormalization(),
    layers.Dropout(0.3),
    layers.Dense(1, activation='sigmoid')
])
model.compile(loss='binary_crossentropy',optimizer='adam', metrics=['accuracy'])


es = EarlyStopping(patience=3, monitor = 'val_accuracy',restore_best_weights = True)
lr = ReduceLROnPlateau(patience = 2, monitor = 'val_loss', factor = 0.5, verbose = 0)

history = model.fit(Training_pad, Y_train, validation_split=0.1, epochs=5, verbose=1, batch_size=32, callbacks=[lr, es]) # fit the training data
loss, accuracy = model.evaluate(Testing_pad, Y_val, batch_size=5) #predicts the output for the given input
# print('loss',loss, 'accuracy', accuracy)
history_df = pd.DataFrame(history.history)
# print('f1_score',f1_score)
print("PHP{:.2f}".format(accuracy))
# accuracy = history_df['accuracy'][3]
# print('accuracy',accuracy)
shap.initjs()
explainer = shap.KernelExplainer(model,Training_pad)
length = 10
tokenizer = token
shap_values = explainer.shap_values(Testing_pad[:length], nsamples=500)
# print("The count of words",token.word_counts)
# print(type(token))
# print(token)
# print(type(Testing_pad))
def get_words(length):
    x_test_words = []
    x_test_token = []
    sentence = []
    for seq in Testing_pad[:length]:
        for token in seq:
            if token == 0:
                sentence.append(token)
            else:
                x_test_token.append(token)
                x_test_words.append(tokenizer.index_word[token])
    return x_test_token, x_test_words, sentence

x_test_token, x_test_words, nonToxic  = get_words(length)

bot_2_idx = np.argsort(nonToxic)[-5:]
top_2_idx = np.argsort(x_test_token)[-5:]

# bot_2_values2 = [nonToxic[i] for i in bot_2_idx]
# # bot_2_values22 = [nonToxic2[i] for i in bot_2_idx]
# top_2_values = [x_test_token[i] for i in top_2_idx]
# top_2_values2 = [x_test_words[i] for i in top_2_idx]

# print('bot_2_values22',bot_2_values22)
# print('bot_2_values2',bot_2_values2)

# print('top_2_values',top_2_values)
# print('top_2_values2',top_2_values2)



