<template>
  <div class="w-2/3 border flex flex-col">
    <!-- Header -->
    <HeaderRight />

    <!-- Messages -->
    <div class="flex-1 overflow-auto" style="background-color: #DAD3CC">
      <div class="py-2 px-3">
        <div class="flex justify-center mb-2">
          <div class="rounded py-2 px-4" style="background-color: #DDECF2">
            <p class="text-sm uppercase">February 20, 2018</p>
          </div>
        </div>

        <div class="flex justify-center mb-4">
          <div class="rounded py-2 px-4" style="background-color: #FCF4CB">
            <p class="text-xs">
              Messages to this chat and calls are now secured with end-to-end
              encryption. Tap for more info.
            </p>
          </div>
        </div>

        <div class="flex mb-2">
          <div class="rounded py-2 px-3" style="background-color: #F2F2F2">
            <p class="text-sm text-teal">Sylverter Stallone</p>
            <p class="text-sm mt-1">Hi everyone! Glad you could join! I am making a new movie.</p>
            <p class="text-right text-xs text-grey-dark mt-1">12:45 pm</p>
          </div>
        </div>

        <div class="flex mb-2">
          <div class="rounded py-2 px-3" style="background-color: #F2F2F2">
            <p class="text-sm text-purple">Tom Cruise</p>
            <p class="text-sm mt-1">Hi all! I have one question for the movie</p>
            <p class="text-right text-xs text-grey-dark mt-1">12:45 pm</p>
          </div>
        </div>

        <div class="flex mb-2">
          <div class="rounded py-2 px-3" style="background-color: #F2F2F2">
            <p class="text-sm text-orange">Harrison Ford</p>
            <p class="text-sm mt-1">Again?</p>
            <p class="text-right text-xs text-grey-dark mt-1">12:45 pm</p>
          </div>
        </div>

        <div class="flex mb-2">
          <div class="rounded py-2 px-3" style="background-color: #F2F2F2">
            <p class="text-sm text-orange">Russell Crowe</p>
            <p class="text-sm mt-1">Is Andrés coming for this one?</p>
            <p class="text-right text-xs text-grey-dark mt-1">12:45 pm</p>
          </div>
        </div>

        <div class="flex mb-2">
          <div class="rounded py-2 px-3" style="background-color: #F2F2F2">
            <p class="text-sm text-teal">Sylverter Stallone</p>
            <p class="text-sm mt-1">He is. Just invited him to join.</p>
            <p class="text-right text-xs text-grey-dark mt-1">12:45 pm</p>
          </div>
        </div>

        <div class="flex justify-end mb-2">
          <div class="rounded py-2 px-3" style="background-color: #E2F7CB">
            <p class="text-sm mt-1">Hi guys.</p>
            <p class="text-right text-xs text-grey-dark mt-1">12:45 pm</p>
          </div>
        </div>

        <div class="flex justify-end mb-2">
          <div class="rounded py-2 px-3" style="background-color: #E2F7CB">
            <p class="text-sm mt-1">Count me in</p>
            <p class="text-right text-xs text-grey-dark mt-1">12:45 pm</p>
          </div>
        </div>

        <div class="flex mb-2">
          <div class="rounded py-2 px-3" style="background-color: #F2F2F2">
            <p class="text-sm text-purple">Tom Cruise</p>
            <p class="text-sm mt-1">Get Andrés on this movie ASAP!</p>
            <p class="text-right text-xs text-grey-dark mt-1">12:45 pm</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Input -->
    <div class="bg-grey-lighter px-4 py-4 flex items-center">
      <div>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
          <path
            opacity=".45"
            fill="#263238"
            d="M9.153 11.603c.795 0 1.439-.879 1.439-1.962s-.644-1.962-1.439-1.962-1.439.879-1.439 1.962.644 1.962 1.439 1.962zm-3.204 1.362c-.026-.307-.131 5.218 6.063 5.551 6.066-.25 6.066-5.551 6.066-5.551-6.078 1.416-12.129 0-12.129 0zm11.363 1.108s-.669 1.959-5.051 1.959c-3.505 0-5.388-1.164-5.607-1.959 0 0 5.912 1.055 10.658 0zM11.804 1.011C5.609 1.011.978 6.033.978 12.228s4.826 10.761 11.021 10.761S23.02 18.423 23.02 12.228c.001-6.195-5.021-11.217-11.216-11.217zM12 21.354c-5.273 0-9.381-3.886-9.381-9.159s3.942-9.548 9.215-9.548 9.548 4.275 9.548 9.548c-.001 5.272-4.109 9.159-9.382 9.159zm3.108-9.751c.795 0 1.439-.879 1.439-1.962s-.644-1.962-1.439-1.962-1.439.879-1.439 1.962.644 1.962 1.439 1.962z"
          />
        </svg>
      </div>
      <div class="flex-1 mx-4">
        <input class="w-full border rounded px-2 py-2" type="text" />
      </div>
      <div>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
          <path
            fill="#263238"
            fill-opacity="0.45"
            d="M11.999 14.942c2.001 0 3.531-1.53 3.531-3.531V4.35c0-2.001-1.53-3.531-3.531-3.531S8.469 2.35 8.469 4.35v7.061c0 2.001 1.53 3.531 3.53 3.531zm6.238-3.53c0 3.531-2.942 6.002-6.237 6.002s-6.237-2.471-6.237-6.002H3.761c0 4.001 3.178 7.297 7.061 7.885v3.884h2.354v-3.884c3.884-.588 7.061-3.884 7.061-7.885h-2z"
          />
        </svg>
      </div>
    </div>
  </div>
</template>

<script>
import HeaderRight from "@/Pages/ChatComponents/HeaderRight";
export default {
    components:{
        HeaderRight
    }
};
</script>

<style>
</style>