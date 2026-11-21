import { test } from 'node:test';
import assert from 'node:assert/strict';
import { searchSuggestions } from '../../resources/js/search-suggestions.js';

test('late suggestion responses never overwrite the current query', async (t) => {
    const requests=[];
    t.mock.method(globalThis, 'fetch', () => new Promise(resolve => requests.push(resolve)));
    const search=searchSuggestions('old');
    const first=search.fetch();
    search.q='new'; const second=search.fetch();
    requests[1]({ok:true,json:async()=>({posts:[{id:2}]})}); await second;
    requests[0]({ok:true,json:async()=>({posts:[{id:1}]})}); await first;
    assert.equal(search.results.posts[0].id,2);
    assert.equal(search.loading,false);
});

test('clearing the query invalidates a pending result', async (t) => {
    let resolve;
    t.mock.method(globalThis, 'fetch', () => new Promise(r => resolve=r));
    const search=searchSuggestions('research'); const pending=search.fetch();
    search.q=''; await search.fetch();
    resolve({ok:true,json:async()=>({posts:[{id:1}]})}); await pending;
    assert.equal(search.open,false);
    assert.equal(search.loading,false);
});

test('HTTP failures show recoverable feedback and stop the loading indicator', async(t) => {
    t.mock.method(globalThis,'fetch',async()=>({ok:false}));
    const search=searchSuggestions('research'); await search.fetch();
    assert.match(search.error,/Press Enter/);
    assert.equal(search.open,false);
    assert.equal(search.loading,false);
});
