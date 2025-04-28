 <template>
  <div class="min-h-screen">
    <header class="header p-6 shadow-md">
      <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-3xl font-bold flex items-center">
          <span class="star-emoji">✨</span>
          <span class="ml-2">Cosmic Stars Blog</span>
        </h1>
        <nav>
          <router-link to="/" class="mr-4">Home</router-link>
          <router-link to="/about" class="mr-4">About Stars</router-link>
          <router-link to="/subscribe" class="btn-primary px-4 py-2 rounded">Subscribe</router-link>
        </nav>
      </div>
    </header>

    <main class="container mx-auto py-8 px-4">
      <section class="mb-12">
        <h2 class="text-4xl font-bold mb-6">Latest Star Discoveries</h2>
        <div class="grid md:grid-cols-3 gap-8">
          <BlogPostCard 
            v-for="post in featuredPosts" 
            :key="post.id"
            :post="post"
          />
        </div>
      </section>

      <QuizSection v-if="showQuiz" />
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import BlogPostCard from '@/Components/BlogPostCard.vue';
import QuizSection from '@/Components/QuizSection.vue';

const featuredPosts = ref([
  {
    id: 1,
    title: 'The Lifecycle of Stars',
    excerpt: 'Explore how stars are born, live, and die in the cosmos.',
    image: '/images/star-lifecycle.jpg'
  },
  {
    id: 2,
    title: 'How Stars Influence Business',
    excerpt: 'Ancient star navigation techniques applied to modern business strategies.',
    image: '/images/stars-business.jpg'
  }
]);

const showQuiz = ref(false);
</script>
