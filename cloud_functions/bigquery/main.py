# Created by yas 2018/11/06

import json
import hashlib
import redis
from flask import Flask, jsonify, request, Response
from flask_cors import CORS
from google.cloud import bigquery
import time

app = Flask(__name__)
CORS(app)

GCP_PROJECT_ID = '<GCP_PROJECT_ID>'
GCP_DATASET    = '<GCP_DATASET>'
GCP_TABLE_NAME = 'Products'

COUNT = 10

REDIS_CONN = None
REDIS_HOST = '<REDIS_HOST>'
REDIS_PORT = 6379

REDIS_CONFIG = {
  'host': REDIS_HOST,
  'port': REDIS_PORT
}

BIGQUERY_CLIENT = None

@app.route('/')
def autocomplete(request):

    start_time = time.time()

    global BIGQUERY_CLIENT
    global REDIS_CONN

    # Parse typed string from a client.
    typed_string = ''
    request_json = request.get_json()
    if request.args and 'term' in request.args:
        typed_string = request.args.get('term')
    elif request_json and 'term' in request_json:
        typed_string = request_json['term']
    else:
        response = jsonify([{}])
        response.headers.add('Access-Control-Allow-Origin', '*')
        return response

    if not REDIS_CONN:
        try:
            REDIS_CONN = redis.Redis(host = REDIS_CONFIG['host'], port = REDIS_CONFIG['port'])
        except redis.ConnectionError:
            error_msg = 'Error: Redis Server not available'
            response = jsonify([].append({'label': error_msg,
                                          'value': error_msg}))
            response.headers.add('Access-Control-Allow-Origin', '*')
            return response

    redis_key = hashlib.md5(typed_string.encode('utf-8')).hexdigest()
    cached_results = REDIS_CONN.get(redis_key)
    if cached_results:
        matches = json.loads(cached_results)
        response = jsonify(matches)
        response.headers.add('Access-Control-Allow-Origin', '*')

        elapsed_time = (time.time() - start_time) * 1000  # Performance Measurement
        print ('Processing time: {0:.0f} ms (cached) [ {1} ] => [ {2} ]'
               .format(elapsed_time,
                       typed_string,
                       matches[0]['label']))
        return response

    # Initialize Datastore Client
    if not BIGQUERY_CLIENT:
        try:
            BIGQUERY_CLIENT = bigquery.Client()
        except:
            import traceback
            traceback.print_exc()

    # Create query and get the results
    query = ("SELECT `name` FROM `{0}.{1}.{2}` WHERE `name` LIKE '{3}%' ORDER BY `name` ASC LIMIT {4}"
             .format(GCP_PROJECT_ID, GCP_DATASET, GCP_TABLE_NAME, typed_string, COUNT))
    query_job = BIGQUERY_CLIENT.query(query)  # API request
    results = query_job.result()  # Waits for query to finish

    # Create response data
    matches = []
    for result in results:
        matches.append({'label': result.name,
                        'value': result.name})
    response = jsonify(matches)
    REDIS_CONN.set(redis_key, json.dumps(matches))
    response.headers.add('Access-Control-Allow-Origin', '*')

    elapsed_time = (time.time() - start_time) * 1000  # Performance Measurement
    print ('Processing time: {0:.0f} ms [ {1} ] => [ {2} ]'
           .format(elapsed_time,
                   typed_string,
                   matches[0]['label']))
    return response
