<template>
  <div class="reader-wrap">

    <!-- Top Nav -->
    <div class="reader-topbar">
      <router-link :to="`/user/books/${$route.params.slug}`" class="topbar-back">← Kembali</router-link>
      <div class="topbar-title" v-if="data">
        <span class="topbar-book">{{ data.book?.title }}</span>
        <span class="topbar-sep">·</span>
        <span class="topbar-chap">Bab {{ data.chapter?.chapter_number }}</span>
      </div>
      <button class="topbar-font" @click="showSettings = !showSettings">Aa</button>
    </div>

    <!-- Settings Dropdown -->
    <div v-if="showSettings" class="settings-panel">
      <div class="sp-label">Ukuran Font</div>
      <div class="font-btns">
        <button @click="fontSize = Math.max(14, fontSize-2)">A−</button>
        <span>{{ fontSize }}px</span>
        <button @click="fontSize = Math.min(24, fontSize+2)">A+</button>
      </div>
      <div class="sp-label" style="margin-top:12px">Lebar</div>
      <div class="font-btns">
        <button @click="narrow = !narrow">{{ narrow ? 'Lebar' : 'Sempit' }}</button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="reader-loading">
      <div class="spinner"></div>
      <span>Memuat bab...</span>
    </div>

    <!-- Error 401 - belum login -->
    <div v-else-if="needLogin" class="reader-gate">
      <div class="gate-ico">🔒</div>
      <div class="gate-title">Login untuk Membaca</div>
      <div class="gate-sub">Kamu perlu login terlebih dahulu untuk membaca buku ini</div>
      <router-link to="/user/sign-in" class="gate-btn">Login Sekarang</router-link>
    </div>

    <!-- Content -->
    <div v-else-if="data" class="reader-body" :class="{narrow: narrow}">
      <!-- Chapter Header -->
      <div class="chap-header">
        <div class="chap-num-badge">Bab {{ data.chapter.chapter_number }}</div>
        <h1 class="chap-title">{{ data.chapter.title }}</h1>
      </div>

      <!-- Content -->
      <div class="chap-content" :style="{fontSize: fontSize + 'px'}">
        <p v-for="(para, i) in paragraphs" :key="i">{{ para }}</p>
      </div>

      <!-- Navigation -->
      <div class="chap-nav">
        <router-link v-if="data.prev" :to="`/user/books/${$route.params.slug}/read/${data.prev.chapter_number}`" class="nav-btn nav-prev">
          ← Bab {{ data.prev.chapter_number }}: {{ data.prev.title }}
        </router-link>
        <div v-else class="nav-empty"></div>
        <router-link v-if="data.next" :to="`/user/books/${$route.params.slug}/read/${data.next.chapter_number}`" class="nav-btn nav-next">
          Bab {{ data.next.chapter_number }}: {{ data.next.title }} →
        </router-link>
        <div v-else class="nav-empty"></div>
      </div>

      <!-- Comments -->
      <div class="comments-section">
        <div class="comments-title">💬 Komentar Bab Ini</div>
        <div class="comment-form">
          <textarea v-model="commentText" class="comment-input" rows="3" placeholder="Tulis komentarmu..."></textarea>
          <button class="comment-submit" :disabled="!commentText || sendingComment" @click="sendComment">
            {{ sendingComment ? 'Mengirim...' : 'Kirim' }}
          </button>
        </div>
        <div class="comments-list">
          <div v-if="comments.length === 0" class="no-comments">Belum ada komentar di bab ini</div>
          <div class="comment-item" v-for="c in comments" :key="c.id">
            <div class="cm-avatar" :style="{background: grad(c.user_id)}">{{ c.user?.name?.charAt(0) }}</div>
            <div class="cm-body">
              <div class="cm-name">{{ c.user?.name }}</div>
              <div class="cm-text">{{ c.content }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed, onMounted, watch } from "vue";
import { useRoute } from "vue-router";
import ApiService from "@/core/services/ApiService";

export default defineComponent({
  name: "user-read-chapter",
  setup() {
    const route = useRoute();
    const data = ref<any>(null);
    const comments = ref<any[]>([]);
    const loading = ref(true);
    const needLogin = ref(false);
    const showSettings = ref(false);
    const fontSize = ref(17);
    const narrow = ref(true);
    const commentText = ref("");
    const sendingComment = ref(false);

    const grads = ["linear-gradient(135deg,#1a365d,#2b6cb0)","linear-gradient(135deg,#1a4731,#276749)","linear-gradient(135deg,#3b1f6e,#805ad5)","linear-gradient(135deg,#631a1a,#e53e3e)","linear-gradient(135deg,#0d4a4a,#319795)"];
    const grad = (id: number) => grads[id % grads.length];

    const paragraphs = computed(() => {
      if (!data.value?.chapter?.content) return [];
      return data.value.chapter.content.split('\n').filter((p: string) => p.trim());
    });

    const loadChapter = async () => {
      loading.value = true;
      needLogin.value = false;
      try {
        const r = await ApiService.get(`books/${route.params.slug}/chapters/${route.params.number}`);
        data.value = r.data;
        window.scrollTo({ top: 0, behavior: 'smooth' });
        loadComments();
      } catch (e: any) {
        if (e.response?.status === 401) needLogin.value = true;
        data.value = null;
      }
      loading.value = false;
    };

    const loadComments = async () => {
      try {
        const r = await ApiService.query(`books/${data.value?.book?.id}/comments`, {
          params: { chapter_id: data.value?.chapter?.id }
        });
        comments.value = r.data.data ?? r.data;
      } catch {}
    };

    const sendComment = async () => {
      if (!commentText.value.trim()) return;
      sendingComment.value = true;
      try {
        await ApiService.post(`books/${data.value?.book?.id}/comments`, {
          content: commentText.value,
          chapter_id: data.value?.chapter?.id,
        });
        commentText.value = "";
        loadComments();
      } catch {}
      sendingComment.value = false;
    };

    onMounted(loadChapter);
    watch(() => route.params.number, loadChapter);

    return { data, comments, loading, needLogin, showSettings, fontSize, narrow, commentText, sendingComment, paragraphs, grad, sendComment };
  }
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,700;0,900;1,400&family=Lora:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@400;500;600&display=swap');

.reader-wrap { display: flex; flex-direction: column; min-height: 100vh; }

/* TOPBAR */
.reader-topbar { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; background: #080808; border: 1px solid #1a1a1a; border-radius: 14px; margin-bottom: 20px; position: sticky; top: 10px; z-index: 10; backdrop-filter: blur(20px); }
.topbar-back { color: #63b3ed; text-decoration: none; font-size: 13px; font-weight: 600; font-family: 'DM Sans', sans-serif; }
.topbar-back:hover { color: #90cdf4; }
.topbar-title { display: flex; align-items: center; gap: 8px; font-family: 'DM Sans', sans-serif; }
.topbar-book { font-size: 13px; font-weight: 600; color: #e2e8f0; }
.topbar-sep { color: #2d4a6a; }
.topbar-chap { font-size: 13px; color: #4a6080; }
.topbar-font { padding: 6px 12px; background: #111; border: 1px solid #222; border-radius: 8px; color: #a0aec0; font-size: 12px; font-weight: 700; cursor: pointer; font-family: 'DM Sans', sans-serif; transition: all 0.2s; }
.topbar-font:hover { background: #1a1a1a; color: #e2e8f0; }

.settings-panel { background: #0d0d0d; border: 1px solid #222; border-radius: 14px; padding: 16px 20px; margin-bottom: 16px; }
.sp-label { font-size: 11px; color: #4a6080; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; font-family: 'DM Sans', sans-serif; }
.font-btns { display: flex; align-items: center; gap: 12px; }
.font-btns button { padding: 6px 14px; background: #111; border: 1px solid #333; border-radius: 8px; color: #a0aec0; font-size: 13px; cursor: pointer; font-family: 'DM Sans', sans-serif; transition: all 0.2s; }
.font-btns button:hover { background: #222; color: #e2e8f0; }
.font-btns span { font-size: 13px; color: #718096; font-family: 'DM Sans', sans-serif; min-width: 40px; text-align: center; }

.reader-loading { display: flex; align-items: center; justify-content: center; gap: 12px; padding: 80px; color: #4a6080; font-family: 'DM Sans', sans-serif; }
.spinner { width: 24px; height: 24px; border: 2px solid #1a1a1a; border-top-color: #63b3ed; border-radius: 50%; animation: spin 0.8s linear infinite; }
@keyframes spin { to{transform:rotate(360deg)} }

.reader-gate { display: flex; flex-direction: column; align-items: center; gap: 12px; padding: 80px 20px; text-align: center; }
.gate-ico { font-size: 48px; }
.gate-title { font-family: 'Fraunces', serif; font-size: 22px; font-weight: 900; color: #e2e8f0; }
.gate-sub { font-size: 14px; color: #4a6080; font-family: 'DM Sans', sans-serif; }
.gate-btn { padding: 12px 28px; background: linear-gradient(135deg,#2b6cb0,#1a365d); color: white; border-radius: 12px; text-decoration: none; font-size: 14px; font-weight: 700; font-family: 'DM Sans', sans-serif; transition: all 0.2s; margin-top: 4px; }
.gate-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(43,108,176,0.4); color: white; }

/* READER BODY */
.reader-body { max-width: 100%; margin: 0 auto; display: flex; flex-direction: column; gap: 32px; }
.reader-body.narrow { max-width: 680px; }

.chap-header { text-align: center; padding: 32px 0 16px; border-bottom: 1px solid #1a1a1a; }
.chap-num-badge { display: inline-block; background: linear-gradient(135deg,#2b6cb0,#1a365d); color: white; font-size: 11px; font-weight: 800; padding: 4px 14px; border-radius: 100px; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 14px; font-family: 'DM Sans', sans-serif; }
.chap-title { font-family: 'Fraunces', serif; font-size: 28px; font-weight: 900; color: #f0f4ff; margin: 0; line-height: 1.3; }

.chap-content { line-height: 2; color: #c4cde0; font-family: 'Lora', serif; }
.chap-content p { margin: 0 0 1.4em; text-indent: 2em; }
.chap-content p:first-child::first-letter { font-family: 'Fraunces', serif; font-size: 3.2em; font-weight: 900; color: #63b3ed; float: left; line-height: 0.8; margin: 0.06em 0.1em 0 0; }

.chap-nav { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; padding-top: 24px; border-top: 1px solid #1a1a1a; }
.nav-btn { padding: 14px 18px; background: #0d0d0d; border: 1px solid #1a1a1a; border-radius: 14px; color: #a0aec0; text-decoration: none; font-size: 13px; font-weight: 600; transition: all 0.2s; font-family: 'DM Sans', sans-serif; line-height: 1.4; }
.nav-btn:hover { border-color: #2b6cb0; color: #63b3ed; background: #0a1628; }
.nav-prev { text-align: left; }
.nav-next { text-align: right; }
.nav-empty {}

/* COMMENTS */
.comments-section { background: #0d0d0d; border: 1px solid #1a1a1a; border-radius: 16px; padding: 22px; }
.comments-title { font-size: 15px; font-weight: 700; color: #e2e8f0; margin-bottom: 16px; font-family: 'Fraunces', serif; }
.comment-form { margin-bottom: 20px; }
.comment-input { width: 100%; background: #111; border: 1.5px solid #1a1a1a; border-radius: 12px; padding: 10px 14px; color: #e2e8f0; font-size: 13px; resize: vertical; outline: none; transition: border-color 0.2s; font-family: 'DM Sans', sans-serif; margin-bottom: 8px; }
.comment-input:focus { border-color: #2b6cb0; }
.comment-input::placeholder { color: #2d4a6a; }
.comment-submit { padding: 9px 20px; background: linear-gradient(135deg,#2b6cb0,#1a365d); border: none; border-radius: 10px; color: white; font-size: 13px; font-weight: 700; cursor: pointer; font-family: 'DM Sans', sans-serif; transition: all 0.2s; }
.comment-submit:disabled { opacity: 0.5; cursor: not-allowed; }
.no-comments { color: #4a6080; font-size: 13px; text-align: center; padding: 16px; font-family: 'DM Sans', sans-serif; }
.comments-list { display: flex; flex-direction: column; gap: 12px; }
.comment-item { display: flex; gap: 12px; }
.cm-avatar { width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 800; color: white; flex-shrink: 0; font-family: 'DM Sans', sans-serif; }
.cm-body { flex: 1; }
.cm-name { font-size: 12px; font-weight: 700; color: #a0aec0; margin-bottom: 3px; font-family: 'DM Sans', sans-serif; }
.cm-text { font-size: 13px; color: #718096; line-height: 1.6; font-family: 'DM Sans', sans-serif; }

@media(max-width:640px) { .chap-title{font-size:22px;} .chap-nav{grid-template-columns:1fr;} .reader-body.narrow{max-width:100%;} }
</style>